<?php

namespace App\Http\Controllers\Api\Administracao\UsuariosPorEntidade;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria;
use App\Models\Administracao\Municipio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuariosPorEntidadeController extends Controller
{
    /**
     * Lista entidades com contador de usuários
     */
    public function listarEntidades(Request $request): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['gerenciar_usuarios']);

        try {
            $query = EntidadeOrcamentaria::query()
                ->select([
                    'entidades_orcamentarias.id',
                    'entidades_orcamentarias.nome_fantasia',
                    'entidades_orcamentarias.ativo'
                ])
                ->with(['municipio:id,nome,codigo_ibge'])
                ->withCount(['usuarios as usuarios_ativos_count' => function ($q) {
                    $q->where('user_entidades_orcamentarias.ativo', true);
                }])
                ->withCount(['usuarios as usuarios_total_count']);

            // Filtros
            if ($request->filled('municipio_id')) {
                $query->whereHas('municipio', function ($q) use ($request) {
                    $q->where('id', $request->municipio_id);
                });
            }

            if ($request->filled('ativo')) {
                $query->where('entidades_orcamentarias.ativo', $request->ativo === 'true');
            }

            if ($request->filled('busca')) {
                $busca = trim($request->busca);
                $query->where('entidades_orcamentarias.nome_fantasia', 'LIKE', "%{$busca}%");
            }

            // Ordenação
            $query->orderBy('entidades_orcamentarias.nome_fantasia');

            // Paginação
            $entidades = $query->paginate($request->get('per_page', 15));

            return response()->json($entidades);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar entidades',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista usuários de uma entidade específica
     */
    public function listarUsuariosPorEntidade(Request $request, int $entidadeId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['gerenciar_usuarios']);

        try {
            $entidade = EntidadeOrcamentaria::with('municipio')->findOrFail($entidadeId);

            $query = User::query()
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.is_active',
                    'user_entidades_orcamentarias.ativo as vinculo_ativo',
                    'user_entidades_orcamentarias.data_vinculacao',
                    'vinculado_por.name as vinculado_por_nome'
                ])
                ->join('user_entidades_orcamentarias', 'users.id', '=', 'user_entidades_orcamentarias.user_id')
                ->leftJoin('users as vinculado_por', 'user_entidades_orcamentarias.vinculado_por_user_id', '=', 'vinculado_por.id')
                ->where('user_entidades_orcamentarias.entidade_orcamentaria_id', $entidadeId);

            // Filtros
            if ($request->filled('vinculo_ativo')) {
                $query->where('user_entidades_orcamentarias.ativo', $request->vinculo_ativo === 'true');
            }

            if ($request->filled('user_ativo')) {
                $query->where('users.is_active', $request->user_ativo === 'true');
            }

            if ($request->filled('busca')) {
                $busca = trim($request->busca);
                $query->where(function ($q) use ($busca) {
                    $q->where('users.name', 'LIKE', "%{$busca}%")
                      ->orWhere('users.email', 'LIKE', "%{$busca}%");
                });
            }

            // Ordenação
            $query->orderBy('user_entidades_orcamentarias.ativo', 'desc')
                  ->orderBy('users.name');

            // Paginação
            $usuarios = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'entidade' => $entidade,
                'usuarios' => $usuarios
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar usuários da entidade',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista usuários disponíveis para vincular à entidade
     */
    public function listarUsuariosDisponiveis(Request $request, int $entidadeId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['gerenciar_usuarios']);

        try {
            $query = User::query()
                ->select('id', 'name', 'email', 'is_active')
                ->where('is_active', true)
                ->whereNotExists(function ($q) use ($entidadeId) {
                    $q->select(DB::raw(1))
                      ->from('user_entidades_orcamentarias')
                      ->whereRaw('user_entidades_orcamentarias.user_id = users.id')
                      ->where('user_entidades_orcamentarias.entidade_orcamentaria_id', $entidadeId)
                      ->where('user_entidades_orcamentarias.ativo', true);
                });

            if ($request->filled('busca')) {
                $busca = trim($request->busca);
                $query->where(function ($q) use ($busca) {
                    $q->where('name', 'LIKE', "%{$busca}%")
                      ->orWhere('email', 'LIKE', "%{$busca}%");
                });
            }

            $query->orderBy('name');

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
     * Vincula um usuário à entidade
     */
    public function vincularUsuario(Request $request, int $entidadeId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['gerenciar-usuarios']);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Verificar se a entidade existe
            $entidade = EntidadeOrcamentaria::findOrFail($entidadeId);

            // Verificar se o usuário existe
            $usuario = User::findOrFail($request->user_id);

            // Verificar se já existe vínculo ativo
            $vinculoExistente = DB::table('user_entidades_orcamentarias')
                ->where('user_id', $request->user_id)
                ->where('entidade_orcamentaria_id', $entidadeId)
                ->where('ativo', true)
                ->first();

            if ($vinculoExistente) {
                return response()->json([
                    'message' => 'Usuário já está vinculado a esta entidade'
                ], 400);
            }

            // Verificar se existe vínculo inativo e reativar
            $vinculoInativo = DB::table('user_entidades_orcamentarias')
                ->where('user_id', $request->user_id)
                ->where('entidade_orcamentaria_id', $entidadeId)
                ->where('ativo', false)
                ->first();

            if ($vinculoInativo) {
                // Reativar vínculo existente
                DB::table('user_entidades_orcamentarias')
                    ->where('id', $vinculoInativo->id)
                    ->update([
                        'ativo' => true,
                        'data_vinculacao' => now(),
                        'vinculado_por_user_id' => Auth::id(),
                        'updated_at' => now()
                    ]);
            } else {
                // Criar novo vínculo
                DB::table('user_entidades_orcamentarias')->insert([
                    'user_id' => $request->user_id,
                    'entidade_orcamentaria_id' => $entidadeId,
                    'ativo' => true,
                    'data_vinculacao' => now(),
                    'vinculado_por_user_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Usuário vinculado com sucesso à entidade!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao vincular usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desvincula um usuário da entidade
     */
    public function desvincularUsuario(int $entidadeId, int $userId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['gerenciar_usuarios']);

        try {
            DB::beginTransaction();

            // Verificar se o vínculo existe
            $vinculo = DB::table('user_entidades_orcamentarias')
                ->where('user_id', $userId)
                ->where('entidade_orcamentaria_id', $entidadeId)
                ->where('ativo', true)
                ->first();

            if (!$vinculo) {
                return response()->json([
                    'message' => 'Vínculo não encontrado ou já inativo'
                ], 404);
            }

            // Desativar vínculo
            DB::table('user_entidades_orcamentarias')
                ->where('id', $vinculo->id)
                ->update([
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
                'message' => 'Erro ao desvincular usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reativa um vínculo inativo
     */
    public function reativarVinculo(int $entidadeId, int $userId): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['gerenciar_usuarios']);

        try {
            DB::beginTransaction();

            // Verificar se existe vínculo inativo
            $vinculo = DB::table('user_entidades_orcamentarias')
                ->where('user_id', $userId)
                ->where('entidade_orcamentaria_id', $entidadeId)
                ->where('ativo', false)
                ->first();

            if (!$vinculo) {
                return response()->json([
                    'message' => 'Vínculo inativo não encontrado'
                ], 404);
            }

            // Reativar vínculo
            DB::table('user_entidades_orcamentarias')
                ->where('id', $vinculo->id)
                ->update([
                    'ativo' => true,
                    'data_vinculacao' => now(),
                    'vinculado_por_user_id' => Auth::id(),
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'message' => 'Vínculo reativado com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao reativar vínculo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dados para filtros
     */
    public function filtros(): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['gerenciar-usuarios']);

        try {
            $municipios = Municipio::select('id', 'nome')
                ->orderBy('nome')
                ->get();

            return response()->json([
                'municipios' => $municipios
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar filtros',
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