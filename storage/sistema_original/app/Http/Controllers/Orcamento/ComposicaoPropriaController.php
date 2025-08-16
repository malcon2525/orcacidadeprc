<?php

namespace App\Http\Controllers\Orcamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orcamento\ComposicaoPropria;
use App\Models\Orcamento\ComposicaoPropriaItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComposicaoPropriaController extends Controller
{
    public function index()
    {
        return view('orcamento.composicao-propria.index');
    }

    public function listar(Request $request)
    {
        $query = \App\Models\Orcamento\ComposicaoPropria::query();

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        $composicoes = $query->orderByDesc('id')->paginate(10);

        // Adiciona os itens de cada composição (se necessário para edição)
        $composicoes->getCollection()->transform(function ($item) {
            $item->itens = $item->itens()->get();
            return $item;
        });

        return response()->json([
            'data' => $composicoes->items(),
            'current_page' => $composicoes->currentPage(),
            'from' => $composicoes->firstItem(),
            'to' => $composicoes->lastItem(),
            'total' => $composicoes->total(),
            'last_page' => $composicoes->lastPage(),
        ]);
    }

    public function create()
    {
        return view('orcamento.composicao-propria.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:20',
            'descricao' => 'required|string|max:255',
            'unidade' => 'required|string|max:10',
            'valor_total_mat_equip' => 'required|numeric',
            'valor_total_mao_obra' => 'required|numeric',
            'valor_total_geral' => 'required|numeric',
            'itens' => 'required|array|min:1',
            'itens.*.referencia' => 'required|string|max:20',
            'itens.*.codigo_item' => 'required|string|max:10',
            'itens.*.descricao' => 'required|string',
            'itens.*.unidade' => 'required|string|max:5',
            'itens.*.coeficiente' => 'required|numeric',
            'itens.*.valor_mat_equip' => 'required|numeric',
            'itens.*.valor_mao_obra' => 'required|numeric',
            'itens.*.valor_total' => 'required|numeric',
            'itens.*.valor_mat_equip_ajustado' => 'required|numeric',
            'itens.*.valor_mao_obra_ajustado' => 'required|numeric',
            'itens.*.valor_total_ajustado' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
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
            return response()->json(['success' => true, 'mensagem' => 'Composição cadastrada com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao cadastrar composição: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'mensagem' => 'Erro ao cadastrar composição.', 'erro' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $composicao = \App\Models\Orcamento\ComposicaoPropria::with('itens')->findOrFail($id);
        return response()->json($composicao);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:20',
            'descricao' => 'required|string|max:255',
            'unidade' => 'required|string|max:10',
            'valor_total_mat_equip' => 'required|numeric',
            'valor_total_mao_obra' => 'required|numeric',
            'valor_total_geral' => 'required|numeric',
            'itens' => 'required|array|min:1',
            'itens.*.referencia' => 'required|string|max:20',
            'itens.*.codigo_item' => 'required|string|max:10',
            'itens.*.descricao' => 'required|string',
            'itens.*.unidade' => 'required|string|max:5',
            'itens.*.coeficiente' => 'required|numeric',
            'itens.*.valor_mat_equip' => 'required|numeric',
            'itens.*.valor_mao_obra' => 'required|numeric',
            'itens.*.valor_total' => 'required|numeric',
            'itens.*.valor_mat_equip_ajustado' => 'required|numeric',
            'itens.*.valor_mao_obra_ajustado' => 'required|numeric',
            'itens.*.valor_total_ajustado' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $composicao = ComposicaoPropria::findOrFail($id);
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
            return response()->json(['success' => true, 'mensagem' => 'Composição atualizada com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao atualizar composição: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'mensagem' => 'Erro ao atualizar composição.', 'erro' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $composicao = \App\Models\Orcamento\ComposicaoPropria::findOrFail($id);
        $composicao->delete(); // onDelete('cascade') garante que os itens também serão removidos
        return response()->json(['success' => true, 'mensagem' => 'Composição excluída com sucesso!']);
    }
}
