<?php

namespace App\Http\Controllers\Api\Administracao\UsuariosPorEntidade;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuariosPorEntidadeController extends Controller
{
    /**
     * Lista entidades orçamentárias para seleção
     */
    public function entidades(): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        try {
            $entidades = EntidadeOrcamentaria::select('id', 'nome', 'is_active')
                ->where('is_active', true)
                ->orderBy('nome')
                ->get();

            return response()->json($entidades);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar entidades',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista usuários vinculados a uma entidade específica
     */
    public function usuariosPorEntidade(Request $request, int $entidadeId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        try {
            $entidade = EntidadeOrcamentaria::findOrFail($entidadeId);

            $query = User::select('users.id', 'users.name', 'users.email', 'users.is_active', 'users.municipio_id')
                ->with(['municipio:id,nome'])
                ->whereHas('entidadesOrcamentarias', function ($q) use ($entidadeId) {
                    $q->where('entidade_orcamentaria_id', $entidadeId);
                })
                ->withPivot('ativo', 'data_vinculacao', 'vinculado_por_user_id');

            // Filtros
            if ($request->filled('busca')) {
                $busca = $request->busca;
                $query->where(function ($q) use ($busca) {
                    $q->where('name', 'LIKE', "%{$busca}%")
                      ->orWhere('email', 'LIKE', "%{$busca}%");
                });
            }

            if ($request->filled('ativo')) {
                $ativo = $request->boolean('ativo');
                $query->whereHas('entidadesOrcamentarias', function ($q) use ($entidadeId, $ativo) {
                    $q->where('entidade_orcamentaria_id', $entidadeId)
                      ->where('user_entidades_orcamentarias.ativo', $ativo);
                });
            }

            // Ordenação
            $query->orderBy('name');

            // Paginação
            $usuarios = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'entidade' => $entidade,
                'usuarios' => $usuarios
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar usuários',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista usuários disponíveis para vinculação (não vinculados à entidade)
     */
    public function usuariosDisponiveis(Request $request, int $entidadeId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        try {
            $query = User::select('id', 'name', 'email', 'municipio_id')
                ->with(['municipio:id,nome'])
                ->where('is_active', true)
                ->whereDoesntHave('entidadesOrcamentarias', function ($q) use ($entidadeId) {
                    $q->where('entidade_orcamentaria_id', $entidadeId)
                      ->where('user_entidades_orcamentarias.ativo', true);
                });

            // Filtro de busca
            if ($request->filled('busca')) {
                $busca = $request->busca;
                $query->where(function ($q) use ($busca) {
                    $q->where('name', 'LIKE', "%{$busca}%")
                      ->orWhere('email', 'LIKE', "%{$busca}%");
                });
            }

            // Ordenação
            $query->orderBy('name');

            // Paginação
            $usuarios = $query->paginate($request->get('per_page', 10));

            return response()->json($usuarios);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar usuários disponíveis',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vincula usuários a uma entidade
     */
    public function vincularUsuarios(Request $request, int $entidadeId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $entidade = EntidadeOrcamentaria::findOrFail($entidadeId);
            $userIds = $request->user_ids;
            $vinculadorId = Auth::id();

            // Preparar dados para inserção
            $vinculacoes = [];
            foreach ($userIds as $userId) {
                $vinculacoes[$userId] = [
                    'ativo' => true,
                    'data_vinculacao' => now(),
                    'vinculado_por_user_id' => $vinculadorId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Vincular usuários (sem remover existentes)
            $entidade->users()->syncWithoutDetaching($vinculacoes);

            DB::commit();

            return response()->json([
                'message' => 'Usuários vinculados com sucesso!',
                'vinculados' => count($userIds)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao vincular usuários',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove vinculação de um usuário com a entidade
     */
    public function desvincularUsuario(Request $request, int $entidadeId, int $userId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        try {
            DB::beginTransaction();

            $entidade = EntidadeOrcamentaria::findOrFail($entidadeId);
            $user = User::findOrFail($userId);

            // Verificar se existe vinculação
            $vinculacao = $entidade->users()->where('user_id', $userId)->first();
            
            if (!$vinculacao) {
                return response()->json([
                    'error' => 'Usuário não está vinculado a esta entidade'
                ], 400);
            }

            // Desativar vinculação (não remove historicamente)
            $entidade->users()->updateExistingPivot($userId, [
                'ativo' => false,
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Usuário desvinculado com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao desvincular usuário',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reativa vinculação de um usuário com a entidade
     */
    public function reativarUsuario(Request $request, int $entidadeId, int $userId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        try {
            DB::beginTransaction();

            $entidade = EntidadeOrcamentaria::findOrFail($entidadeId);
            $user = User::findOrFail($userId);

            // Verificar se existe vinculação inativa
            $vinculacao = $entidade->users()->where('user_id', $userId)->first();
            
            if (!$vinculacao) {
                return response()->json([
                    'error' => 'Usuário nunca foi vinculado a esta entidade'
                ], 400);
            }

            // Reativar vinculação
            $entidade->users()->updateExistingPivot($userId, [
                'ativo' => true,
                'vinculado_por_user_id' => Auth::id(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Usuário reativado com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao reativar usuário',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificação de acesso unificada
     */
    private function checkAccess($permissions, $requireAll = false)
    {
        /** @var \App\Models\Administracao\User $user */
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // 2. Verificação flexível de permissões
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }
        
        if ($requireAll) {
            // Todas as permissões são obrigatórias (AND)
            foreach ($permissions as $permission) {
                if (!$user->hasPermission($permission)) {
                    abort(403, "Permissão obrigatória: {$permission}");
                }
            }
        } else {
            // Pelo menos uma permissão é suficiente (OR)
            $hasAnyPermission = false;
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasAnyPermission = true;
                    break;
                }
            }
            
            if (!$hasAnyPermission) {
                abort(403, 'Acesso negado. Permissão insuficiente.');
            }
        }
        
        return true;
    }
}
