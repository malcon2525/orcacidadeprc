<?php

namespace App\Http\Controllers\Api\Administracao\EstruturaOrcamento;

use App\Http\Controllers\Controller;
use App\Models\Orcamento\GrandeItem;
use App\Models\Orcamento\TipoOrcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GrandeItemController extends Controller
{
    /**
     * Lista todos os grandes itens de um tipo de orçamento específico
     */
    public function listarPorTipoOrcamento(Request $request, $tipoOrcamentoId)
    {
        try {
            $grandesItens = GrandeItem::with(['subGrupos' => function($query) {
                    $query->orderBy('ordem');
                }])
                ->where('tipo_orcamento_id', $tipoOrcamentoId)
                ->orderBy('ordem')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $grandesItens
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Cria um novo grande item
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_orcamento_id' => 'required|exists:tipos_orcamentos,id',
            'descricao' => 'required|string|max:100',
            'ordem' => 'required|integer|min:0'
        ], [
            'tipo_orcamento_id.required' => 'O tipo de orçamento é obrigatório',
            'tipo_orcamento_id.exists' => 'O tipo de orçamento selecionado não existe',
            'descricao.required' => 'A descrição é obrigatória',
            'descricao.string' => 'A descrição deve ser um texto',
            'descricao.max' => 'A descrição não pode ter mais de 100 caracteres',
            'ordem.required' => 'A ordem é obrigatória',
            'ordem.integer' => 'A ordem deve ser um número inteiro',
            'ordem.min' => 'A ordem deve ser maior ou igual a zero'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Verificar se já existe um item com a mesma ordem
            $ordemExistente = GrandeItem::where('tipo_orcamento_id', $request->tipo_orcamento_id)
                ->where('ordem', $request->ordem)
                ->exists();

            if ($ordemExistente) {
                // Reordenar itens existentes
                GrandeItem::where('tipo_orcamento_id', $request->tipo_orcamento_id)
                    ->where('ordem', '>=', $request->ordem)
                    ->increment('ordem');
            }

            $grandeItem = GrandeItem::create($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $grandeItem,
                'message' => 'Grande item criado com sucesso'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Atualiza um grande item existente
     */
    public function update(Request $request, $id)
    {
        $grandeItem = GrandeItem::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:100',
            'ordem' => 'required|integer|min:0'
        ], [
            'descricao.required' => 'A descrição é obrigatória',
            'descricao.string' => 'A descrição deve ser um texto',
            'descricao.max' => 'A descrição não pode ter mais de 100 caracteres',
            'ordem.required' => 'A ordem é obrigatória',
            'ordem.integer' => 'A ordem deve ser um número inteiro',
            'ordem.min' => 'A ordem deve ser maior ou igual a zero'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $ordemAntiga = $grandeItem->ordem;
            $novaOrdem = $request->ordem;

            // Se a ordem mudou, reordenar os outros itens
            if ($ordemAntiga !== $novaOrdem) {
                if ($novaOrdem > $ordemAntiga) {
                    // Movendo para baixo
                    GrandeItem::where('tipo_orcamento_id', $grandeItem->tipo_orcamento_id)
                        ->where('ordem', '>', $ordemAntiga)
                        ->where('ordem', '<=', $novaOrdem)
                        ->decrement('ordem');
                } else {
                    // Movendo para cima
                    GrandeItem::where('tipo_orcamento_id', $grandeItem->tipo_orcamento_id)
                        ->where('ordem', '>=', $novaOrdem)
                        ->where('ordem', '<', $ordemAntiga)
                        ->increment('ordem');
                }
            }

            $grandeItem->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $grandeItem,
                'message' => 'Grande item atualizado com sucesso'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Exclui um grande item
     */
    public function destroy($id)
    {
        try {
            $grandeItem = GrandeItem::findOrFail($id);
            
            DB::beginTransaction();

            // Reordenar os itens restantes
            GrandeItem::where('tipo_orcamento_id', $grandeItem->tipo_orcamento_id)
                ->where('ordem', '>', $grandeItem->ordem)
                ->decrement('ordem');

            // Excluir o grande item (os subgrupos serão excluídos em cascade)
            $grandeItem->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Grande item excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Reordena os grandes itens
     */
    public function reordenar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'itens' => 'required|array',
            'itens.*.id' => 'required|exists:grandes_itens,id',
            'itens.*.ordem' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($request->itens as $item) {
                GrandeItem::where('id', $item['id'])->update(['ordem' => $item['ordem']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}
