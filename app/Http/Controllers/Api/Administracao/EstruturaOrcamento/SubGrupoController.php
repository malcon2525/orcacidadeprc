<?php

namespace App\Http\Controllers\Api\Administracao\EstruturaOrcamento;

use App\Http\Controllers\Controller;
use App\Models\Administracao\EstruturaOrcamento\SubGrupo;
use App\Models\Administracao\EstruturaOrcamento\GrandeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SubGrupoController extends Controller
{
    /**
     * Método index para compatibilidade com apiResource
     */
    public function index(Request $request)
    {
        return $this->listar($request);
    }

    /**
     * Lista todos os subgrupos de um grande item específico
     */
    public function listarPorGrandeItem(Request $request, $grandeItemId)
    {
        try {
            $subgrupos = SubGrupo::where('eo_grande_item_id', $grandeItemId)
                ->orderBy('ordem')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subgrupos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Cria um novo subgrupo
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eo_grande_item_id' => 'required|exists:eo_grandes_itens,id',
            'descricao' => 'required|string|max:100',
            'ordem' => 'required|integer|min:0'
        ], [
            'eo_grande_item_id.required' => 'O grande item é obrigatório',
            'eo_grande_item_id.exists' => 'O grande item selecionado não existe',
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

            // Verificar se já existe um subgrupo com a mesma ordem no mesmo grande item
            $ordemExistente = SubGrupo::where('eo_grande_item_id', $request->eo_grande_item_id)
                ->where('ordem', $request->ordem)
                ->exists();

            if ($ordemExistente) {
                // Reordenar subgrupos existentes
                SubGrupo::where('eo_grande_item_id', $request->eo_grande_item_id)
                    ->where('ordem', '>=', $request->ordem)
                    ->increment('ordem');
            }

            $subgrupo = SubGrupo::create($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $subgrupo,
                'message' => 'Subgrupo criado com sucesso'
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
     * Atualiza um subgrupo existente
     */
    public function update(Request $request, $id)
    {
        $subgrupo = SubGrupo::findOrFail($id);

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

            $ordemAntiga = $subgrupo->ordem;
            $novaOrdem = $request->ordem;

            // Se a ordem mudou, reordenar os outros subgrupos do mesmo grande item
            if ($ordemAntiga !== $novaOrdem) {
                if ($novaOrdem > $ordemAntiga) {
                    // Movendo para baixo
                    SubGrupo::where('eo_grande_item_id', $subgrupo->eo_grande_item_id)
                        ->where('ordem', '>', $ordemAntiga)
                        ->where('ordem', '<=', $novaOrdem)
                        ->decrement('ordem');
                } else {
                    // Movendo para cima
                    SubGrupo::where('eo_grande_item_id', $subgrupo->eo_grande_item_id)
                        ->where('ordem', '>=', $novaOrdem)
                        ->where('ordem', '<', $ordemAntiga)
                        ->increment('ordem');
                }
            }

            $subgrupo->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $subgrupo,
                'message' => 'Subgrupo atualizado com sucesso'
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
     * Exclui um subgrupo
     */
    public function destroy($id)
    {
        try {
            $subgrupo = SubGrupo::findOrFail($id);
            
            DB::beginTransaction();

            // Reordenar os subgrupos restantes do mesmo grande item
            SubGrupo::where('eo_grande_item_id', $subgrupo->eo_grande_item_id)
                ->where('ordem', '>', $subgrupo->ordem)
                ->decrement('ordem');

            $subgrupo->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subgrupo excluído com sucesso'
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
     * Reordena os subgrupos
     */
    public function reordenar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'itens' => 'required|array',
            'itens.*.id' => 'required|exists:eo_sub_itens,id',
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
                SubGrupo::where('id', $item['id'])->update(['ordem' => $item['ordem']]);
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
