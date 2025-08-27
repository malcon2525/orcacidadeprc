<?php

namespace App\Http\Controllers\Api\Orcamento\ComposicaoPropria;

use App\Http\Controllers\Controller;
use App\Models\Orcamento\ComposicaoPropria;
use App\Models\Orcamento\ComposicaoPropriaItem;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ComposicaoPropriaController extends Controller
{
    /**
     * Verifica se o usuário tem acesso à funcionalidade
     */
    private function checkAccess($permissions, $requireAll = false)
    {
        $user = User::find(Auth::id());
        
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

    /**
     * Lista todas as composições próprias com paginação
     */
    public function listar(Request $request)
    {
        $this->checkAccess(['gerenciar_composicoes_proprias', 'visualizar_composicoes_proprias']);
        
        try {
            $query = ComposicaoPropria::query();

            // Aplicar filtros
            if ($request->filled('descricao')) {
                $query->porDescricao($request->descricao);
            }

            if ($request->filled('codigo')) {
                $query->porCodigo($request->codigo);
            }

            // Ordenar por ID decrescente (mais recentes primeiro)
            $composicoes = $query->orderByDesc('id')->paginate(10);

            // Adicionar os itens de cada composição
            $composicoes->getCollection()->transform(function ($item) {
                $item->itens = $item->itens()->get();
                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $composicoes->items(),
                'current_page' => $composicoes->currentPage(),
                'from' => $composicoes->firstItem(),
                'to' => $composicoes->lastItem(),
                'total' => $composicoes->total(),
                'last_page' => $composicoes->lastPage(),
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao listar composições próprias: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar composições próprias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cria uma nova composição própria
     */
    public function store(Request $request)
    {
        $this->checkAccess(['gerenciar_composicoes_proprias'], true);

        try {
            $validator = Validator::make($request->all(), [
                'codigo' => 'nullable|string|max:20',
                'descricao' => 'required|string|max:255',
                'unidade' => 'required|string|max:10',
                'valor_total_mat_equip' => 'required|numeric|min:0',
                'valor_total_mao_obra' => 'required|numeric|min:0',
                'valor_total_geral' => 'required|numeric|min:0',
                'itens' => 'required|array|min:1',
                'itens.*.referencia' => 'required|in:SINAPI,DERPR,PERSONALIZADA',
                'itens.*.codigo_item' => 'required|string|max:10',
                'itens.*.descricao' => 'required|string',
                'itens.*.unidade' => 'required|string|max:5',
                'itens.*.coeficiente' => 'required|numeric|min:0',
                'itens.*.valor_mat_equip' => 'required|numeric|min:0',
                'itens.*.valor_mao_obra' => 'required|numeric|min:0',
                'itens.*.valor_total' => 'required|numeric|min:0',
                'itens.*.valor_mat_equip_ajustado' => 'required|numeric|min:0',
                'itens.*.valor_mao_obra_ajustado' => 'required|numeric|min:0',
                'itens.*.valor_total_ajustado' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $composicao = ComposicaoPropria::create([
                'codigo' => $request->codigo,
                'descricao' => $request->descricao,
                'unidade' => $request->unidade,
                'valor_total_mat_equip' => $request->valor_total_mat_equip,
                'valor_total_mao_obra' => $request->valor_total_mao_obra,
                'valor_total_geral' => $request->valor_total_geral,
            ]);

            foreach ($request->itens as $item) {
                $composicao->itens()->create($item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Composição criada com sucesso!',
                'data' => $composicao->load('itens')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar composição própria: ' . $e->getMessage(), ['exception' => $e]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar composição própria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza uma composição própria existente
     */
    public function update(Request $request, $id)
    {
        $this->checkAccess(['gerenciar_composicoes_proprias'], true);

        try {
            $composicao = ComposicaoPropria::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'codigo' => 'nullable|string|max:20',
                'descricao' => 'required|string|max:255',
                'unidade' => 'required|string|max:10',
                'valor_total_mat_equip' => 'required|numeric|min:0',
                'valor_total_mao_obra' => 'required|numeric|min:0',
                'valor_total_geral' => 'required|numeric|min:0',
                'itens' => 'required|array|min:1',
                'itens.*.referencia' => 'required|in:SINAPI,DERPR,PERSONALIZADA',
                'itens.*.codigo_item' => 'required|string|max:10',
                'itens.*.descricao' => 'required|string',
                'itens.*.unidade' => 'required|string|max:5',
                'itens.*.coeficiente' => 'required|numeric|min:0',
                'itens.*.valor_mat_equip' => 'required|numeric|min:0',
                'itens.*.valor_mao_obra' => 'required|numeric|min:0',
                'itens.*.valor_total' => 'required|numeric|min:0',
                'itens.*.valor_mat_equip_ajustado' => 'required|numeric|min:0',
                'itens.*.valor_mao_obra_ajustado' => 'required|numeric|min:0',
                'itens.*.valor_total_ajustado' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $composicao->update([
                'codigo' => $request->codigo,
                'descricao' => $request->descricao,
                'unidade' => $request->unidade,
                'valor_total_mat_equip' => $request->valor_total_mat_equip,
                'valor_total_mao_obra' => $request->valor_total_mao_obra,
                'valor_total_geral' => $request->valor_total_geral,
            ]);

            // Remove itens antigos e adiciona os novos
            $composicao->itens()->delete();
            foreach ($request->itens as $item) {
                $composicao->itens()->create($item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Composição atualizada com sucesso!',
                'data' => $composicao->load('itens')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar composição própria: ' . $e->getMessage(), ['exception' => $e]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar composição própria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exclui uma composição própria
     */
    public function destroy($id)
    {
        $this->checkAccess(['gerenciar_composicoes_proprias'], true);

        try {
            $composicao = ComposicaoPropria::findOrFail($id);
            $composicao->delete(); // onDelete('cascade') garante que os itens também serão removidos

            return response()->json([
                'success' => true,
                'message' => 'Composição excluída com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao excluir composição própria: ' . $e->getMessage(), ['exception' => $e]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir composição própria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca uma composição específica para edição
     */
    public function edit($id)
    {
        $this->checkAccess(['gerenciar_composicoes_proprias', 'visualizar_composicoes_proprias']);

        try {
            $composicao = ComposicaoPropria::with('itens')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $composicao
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar composição para edição: ' . $e->getMessage(), ['exception' => $e]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar composição para edição',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
