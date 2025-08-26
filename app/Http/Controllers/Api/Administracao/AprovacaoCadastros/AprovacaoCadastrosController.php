<?php

namespace App\Http\Controllers\Api\Administracao\AprovacaoCadastros;

use App\Http\Controllers\Controller;
use App\Models\Administracao\SolicitacaoCadastro;
use App\Models\Administracao\User;
use App\Models\Administracao\Municipio;
use App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AprovacaoCadastrosController extends Controller
{
    /**
     * Lista as solicitações de cadastro pendentes
     */
    public function listar(Request $request): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        try {
            $query = SolicitacaoCadastro::with([
                'user:id,name,email',
                'municipio:id,nome',
                'entidadeOrcamentaria:id,nome_fantasia',
                'aprovadoPor:id,name,email'
            ]);

            // Filtros
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('municipio_id')) {
                $query->where('municipio_id', $request->municipio_id);
            }

            if ($request->filled('entidade_id')) {
                $query->where('entidade_orcamentaria_id', $request->entidade_id);
            }

            if ($request->filled('busca')) {
                $busca = trim($request->busca);
                $query->whereHas('user', function ($q) use ($busca) {
                    $q->where('name', 'LIKE', "%{$busca}%")
                      ->orWhere('email', 'LIKE', "%{$busca}%");
                });
            }

            // Ordenação
            $query->recentes();

            // Paginação
            $solicitacoes = $query->paginate($request->get('per_page', 15));

            return response()->json($solicitacoes);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar solicitações',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aprova uma solicitação de cadastro
     */
    public function aprovar(Request $request, int $id): JsonResponse
    {
        Log::info('Iniciando aprovação de solicitação', [
            'solicitacao_id' => $id,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);
        
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        $validator = Validator::make($request->all(), [
            'observacoes_aprovacao' => 'nullable|string|max:1000',
            'municipio_id' => 'required|exists:municipios,id',
            'entidade_orcamentaria_id' => 'required|exists:entidades_orcamentarias,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Log::info('Iniciando transação de aprovação');
            
            DB::beginTransaction();

            $solicitacao = SolicitacaoCadastro::with('user')->findOrFail($id);
            Log::info('Solicitação encontrada', ['solicitacao' => $solicitacao->toArray()]);

            if ($solicitacao->status !== 'pendente') {
                return response()->json([
                    'message' => 'Esta solicitação já foi processada'
                ], 400);
            }

            Log::info('Atualizando solicitação');
            // Atualizar dados da solicitação
            $solicitacao->update([
                'status' => 'aprovado',
                'data_aprovacao' => now(),
                'aprovado_por_user_id' => Auth::id(),
                'observacoes_aprovacao' => $request->observacoes_aprovacao,
                'municipio_id' => $request->municipio_id,
                'entidade_orcamentaria_id' => $request->entidade_orcamentaria_id
            ]);

            Log::info('Ativando usuário');
            // Ativar o usuário
            $solicitacao->user->update([
                'is_active' => true
            ]);

            Log::info('Vinculando usuário à entidade');
            // Vincular usuário à entidade orçamentária
            try {
                // Inserir diretamente na tabela pivot
                DB::table('user_entidades_orcamentarias')->insertOrIgnore([
                    'user_id' => $solicitacao->user_id,
                    'entidade_orcamentaria_id' => $request->entidade_orcamentaria_id,
                    'ativo' => true,
                    'data_vinculacao' => now(),
                    'vinculado_por_user_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                Log::info('Usuário vinculado com sucesso');
            } catch (\Exception $e) {
                Log::error('Erro ao vincular usuário: ' . $e->getMessage());
                throw $e;
            }

            DB::commit();
            Log::info('Transação commitada com sucesso');

            return response()->json([
                'message' => 'Solicitação aprovada com sucesso!',
                'solicitacao' => $solicitacao->load(['user', 'municipio', 'entidadeOrcamentaria', 'aprovadoPor'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao aprovar solicitação: ' . $e->getMessage(), [
                'solicitacao_id' => $id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Erro interno ao aprovar solicitação',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeita uma solicitação de cadastro
     */
    public function rejeitar(Request $request, int $id): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        $validator = Validator::make($request->all(), [
            'observacoes_aprovacao' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Motivo da rejeição é obrigatório',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $solicitacao = SolicitacaoCadastro::findOrFail($id);

            if ($solicitacao->status !== 'pendente') {
                return response()->json([
                    'error' => 'Esta solicitação já foi processada'
                ], 400);
            }

            $solicitacao->update([
                'status' => 'rejeitado',
                'data_aprovacao' => now(),
                'aprovado_por_user_id' => Auth::id(),
                'observacoes_aprovacao' => $request->observacoes_aprovacao
            ]);

            return response()->json([
                'message' => 'Solicitação rejeitada com sucesso!',
                'solicitacao' => $solicitacao->load(['user', 'municipio', 'entidadeOrcamentaria', 'aprovadoPor'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao rejeitar solicitação',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna dados para os filtros (municípios e entidades)
     */
    public function filtros(): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        try {
            $municipios = Municipio::select('id', 'nome')
                ->orderBy('nome')
                ->get();

            $entidades = EntidadeOrcamentaria::select('id', 'nome_fantasia as nome')
                ->where('ativo', true)
                ->orderBy('nome_fantasia')
                ->get();

            return response()->json([
                'municipios' => $municipios,
                'entidades' => $entidades
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
