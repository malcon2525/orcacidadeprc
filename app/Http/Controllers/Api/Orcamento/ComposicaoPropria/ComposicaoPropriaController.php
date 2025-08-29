<?php

namespace App\Http\Controllers\Api\Orcamento\ComposicaoPropria;

use App\Http\Controllers\Controller;
use App\Models\Orcamento\ComposicaoPropria;
use App\Models\Orcamento\ComposicaoPropriaItem;
use App\Models\Administracao\User;
use App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria;
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
    private function checkAccess()
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(401, 'Usuário não autenticado.');
        }
        
        // 1. Super admin tem acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // 2. Verificar papel criar_orcamentos
        if (!$user->hasRole('criar_orcamentos')) {
            abort(403, 'Acesso negado. É necessário ter o papel "criar_orcamentos".');
        }
        
        // 3. Verificar permissão composicao_propria_crud
        if (!$user->hasPermission('composicao_propria_crud')) {
            abort(403, 'Acesso negado. É necessário ter a permissão "composicao_propria_crud".');
        }
        
        // 4. Verificar vínculo com entidade orçamentária
        $temVinculos = DB::table('user_entidades_orcamentarias')
            ->where('user_id', $user->id)
            ->where('ativo', true)
            ->exists();
        
        if (!$temVinculos) {
            abort(403, 'Acesso negado. Usuário não possui vínculos ativos com entidades orçamentárias.');
        }
        
        // 5. Verificar contexto orçamentário
        if (!\App\Helpers\OrcamentoContextHelper::temContextoDefinido()) {
            abort(403, 'Acesso negado. É necessário configurar o contexto orçamentário antes de realizar operações.');
        }
        
        return true;
    }

    /**
     * Captura as datas do contexto orçamentário do usuário
     */
    private function capturarDatasContexto(): array
    {
        $user = Auth::user();
        $contexto = \App\Models\Orcamento\UserOrcamentoContext::getContextoUsuario($user->id);
        
        if (!$contexto) {
            throw new \Exception('Contexto orçamentário não encontrado para o usuário.');
        }
        
        return [
            'data_base_sinapi' => $contexto->data_base_sinapi->format('Y-m-d'),
            'data_base_derpr' => $contexto->data_base_derpr->format('Y-m-d')
        ];
    }

    /**
     * Adiciona as datas do contexto e desoneração nos itens baseado na referência
     */
    private function adicionarDatasContextoItens(array $itens): array
    {
        $datasContexto = $this->capturarDatasContexto();
        
        foreach ($itens as &$item) {
            // Adiciona as datas e desoneração baseado na referência do item
            if ($item['referencia'] === 'SINAPI') {
                $item['data_base_sinapi'] = $datasContexto['data_base_sinapi'];
                $item['data_base_derpr'] = null; // SINAPI não usa DERPR
                // Usa a desoneração que foi selecionada no zoom (vem do frontend)
                // Se não foi informada, usa 'sem' como padrão
                if (!isset($item['desoneracao'])) {
                    $item['desoneracao'] = 'sem';
                }
            } elseif ($item['referencia'] === 'DERPR') {
                $item['data_base_sinapi'] = null; // DERPR não usa SINAPI
                $item['data_base_derpr'] = $datasContexto['data_base_derpr'];
                // Usa a desoneração que foi selecionada no zoom (vem do frontend)
                // Se não foi informada, usa 'sem' como padrão
                if (!isset($item['desoneracao'])) {
                    $item['desoneracao'] = 'sem';
                }
            } else {
                // PERSONALIZADA não usa nem SINAPI nem DERPR nem desoneração
                $item['data_base_sinapi'] = null;
                $item['data_base_derpr'] = null;
                $item['desoneracao'] = null;
            }
        }
        
        return $itens;
    }

    /**
     * Retorna a entidade orçamentária do contexto atual
     */
    public function listarEntidadesOrcamentarias()
    {
        $this->checkAccess();
        
        try {
            $user = Auth::user();
            $contexto = \App\Models\Orcamento\UserOrcamentoContext::getContextoUsuario($user->id);
            
            if (!$contexto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contexto orçamentário não encontrado.'
                ], 400);
            }

            // Retorna apenas a entidade do contexto
            $entidade = $contexto->entidadeOrcamentaria;
            
            $entidadeFormatada = [
                'id' => $entidade->id,
                'jurisdicao_razao_social' => $entidade->jurisdicao_razao_social,
                'jurisdicao_nome_fantasia' => $entidade->jurisdicao_nome_fantasia,
                'tipo_organizacao' => $entidade->tipo_organizacao,
                'is_from_context' => true // Flag para indicar que vem do contexto
            ];

            return response()->json([
                'success' => true,
                'data' => [$entidadeFormatada], // Array com um único item
                'context_info' => [
                    'entidade_id' => $contexto->entidade_orcamentaria_id,
                    'data_base_sinapi' => $contexto->data_base_sinapi->format('Y-m-d'),
                    'data_base_derpr' => $contexto->data_base_derpr->format('Y-m-d')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar entidade do contexto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Lista todas as composições próprias com paginação
     */
    public function listar(Request $request)
    {
        $this->checkAccess();
        
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
        $this->checkAccess();

        try {
            $validator = Validator::make($request->all(), [
                'entidade_orcamentaria_id' => 'required|exists:entidades_orcamentarias,id',
                'codigo' => 'required|string|max:20',
                'descricao' => 'required|string|max:255',
                'unidade' => 'required|string|max:10',
                'valor_total_mat_equip' => 'required|numeric|min:0',
                'valor_total_mao_obra' => 'required|numeric|min:0',
                'valor_total_geral' => 'required|numeric|min:0.01',
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
            ], [
                'entidade_orcamentaria_id.required' => 'A entidade orçamentária é obrigatória.',
                'entidade_orcamentaria_id.exists' => 'A entidade orçamentária selecionada não existe.',
                'codigo.required' => 'O código é obrigatório.',
                'descricao.required' => 'A descrição é obrigatória.',
                'unidade.required' => 'A unidade é obrigatória.',
                'valor_total_geral.min' => 'O valor total deve ser maior que zero.',
                'itens.required' => 'Pelo menos um item deve ser adicionado à composição.',
                'itens.min' => 'Pelo menos um item deve ser adicionado à composição.',
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
                'entidade_orcamentaria_id' => $request->entidade_orcamentaria_id,
                'codigo' => $request->codigo,
                'descricao' => $request->descricao,
                'unidade' => $request->unidade,
                'valor_total_mat_equip' => $request->valor_total_mat_equip,
                'valor_total_mao_obra' => $request->valor_total_mao_obra,
                'valor_total_geral' => $request->valor_total_geral,
            ]);

            // Adiciona as datas do contexto nos itens antes de salvar
            $itensComDatas = $this->adicionarDatasContextoItens($request->itens);
            
            foreach ($itensComDatas as $item) {
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
        $this->checkAccess();

        try {
            $composicao = ComposicaoPropria::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'entidade_orcamentaria_id' => 'required|exists:entidades_orcamentarias,id',
                'codigo' => 'required|string|max:20',
                'descricao' => 'required|string|max:255',
                'unidade' => 'required|string|max:10',
                'valor_total_mat_equip' => 'required|numeric|min:0',
                'valor_total_mao_obra' => 'required|numeric|min:0',
                'valor_total_geral' => 'required|numeric|min:0.01',
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
            ], [
                'entidade_orcamentaria_id.required' => 'A entidade orçamentária é obrigatória.',
                'entidade_orcamentaria_id.exists' => 'A entidade orçamentária selecionada não existe.',
                'codigo.required' => 'O código é obrigatório.',
                'descricao.required' => 'A descrição é obrigatória.',
                'unidade.required' => 'A unidade é obrigatória.',
                'valor_total_geral.min' => 'O valor total deve ser maior que zero.',
                'itens.required' => 'Pelo menos um item deve ser adicionado à composição.',
                'itens.min' => 'Pelo menos um item deve ser adicionado à composição.',
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
                'entidade_orcamentaria_id' => $request->entidade_orcamentaria_id,
                'codigo' => $request->codigo,
                'descricao' => $request->descricao,
                'unidade' => $request->unidade,
                'valor_total_mat_equip' => $request->valor_total_mat_equip,
                'valor_total_mao_obra' => $request->valor_total_mao_obra,
                'valor_total_geral' => $request->valor_total_geral,
            ]);

            // Remove itens antigos e adiciona os novos
            $composicao->itens()->delete();
            
            // Adiciona as datas do contexto nos itens antes de salvar
            $itensComDatas = $this->adicionarDatasContextoItens($request->itens);
            
            foreach ($itensComDatas as $item) {
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
        $this->checkAccess();

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
        $this->checkAccess();

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
