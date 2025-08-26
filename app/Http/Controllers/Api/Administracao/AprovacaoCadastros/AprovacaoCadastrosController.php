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
                'entidadeOrcamentaria:id,jurisdicao_nome_fantasia,tipo_organizacao,nivel_administrativo',
                'aprovadoPor:id,name,email'
            ]);

            // NOVOS FILTROS CONFORME SOLICITADO
            if ($request->filled('nome')) {
                $query->porNome($request->nome);
            }

            if ($request->filled('email')) {
                $query->porEmail($request->email);
            }

            if ($request->filled('entidade_id')) {
                $query->porEntidade($request->entidade_id);
            }

            if ($request->filled('tipo_organizacao')) {
                $query->whereHas('entidadeOrcamentaria', function ($q) use ($request) {
                    $q->where('tipo_organizacao', $request->tipo_organizacao);
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('visitante_uf')) {
                $query->porUf($request->visitante_uf);
            }

            if ($request->filled('visitante_municipio')) {
                $query->porMunicipio($request->visitante_municipio);
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
            'observacoes_aprovacao' => 'nullable|string|max:1000'
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
                'observacoes_aprovacao' => $request->observacoes_aprovacao
            ]);

            Log::info('Ativando usuário');
            // Ativar o usuário
            $solicitacao->user->update([
                'is_active' => true
            ]);

            Log::info('Vinculando usuário à entidade');
            // Vincular usuário à entidade orçamentária solicitada
            try {
                // Inserir diretamente na tabela pivot
                DB::table('user_entidades_orcamentarias')->insertOrIgnore([
                    'user_id' => $solicitacao->user_id,
                    'entidade_orcamentaria_id' => $solicitacao->entidade_orcamentaria_id,
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
                'solicitacao' => $solicitacao->load(['user', 'entidadeOrcamentaria', 'aprovadoPor'])
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
                'solicitacao' => $solicitacao->load(['user', 'entidadeOrcamentaria', 'aprovadoPor'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao rejeitar solicitação',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna dados para os filtros (entidades e tipos)
     */
    public function filtros(): JsonResponse
    {
        // Verificação de acesso
        $this->checkAccess(['aprovar-cadastros']);

        try {
            $entidades = EntidadeOrcamentaria::select('id', 'jurisdicao_nome_fantasia as nome', 'tipo_organizacao')
                ->where('ativo', true)
                ->orderBy('jurisdicao_nome_fantasia')
                ->get();

            $tiposOrganizacao = EntidadeOrcamentaria::select('tipo_organizacao')
                ->distinct()
                ->orderBy('tipo_organizacao')
                ->pluck('tipo_organizacao');

            return response()->json([
                'entidades' => $entidades,
                'tipos_organizacao' => $tiposOrganizacao
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
