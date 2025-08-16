<?php

namespace App\Http\Controllers\Web\Transporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transportes\CoeficienteCustoTransporte;
use App\Models\Transportes\CustoTransporte;

class CoeficienteCustoTransporteController extends Controller
{
    public function index(Request $request)
    {
        $data_base = $request->query('data_base');
        $desoneracao = $request->query('desoneracao');
        if (!$data_base || !$desoneracao) {
            return response()->json([]);
        }
        $coef = CoeficienteCustoTransporte::where('data_base', $data_base)
            ->where('desoneracao', $desoneracao)
            ->get();
        return response()->json($coef);
    }

    public function store(Request $request)
    {
        $data = $request->only(['data_base', 'desoneracao', 'custo_transporte_id', 'x1', 'x2', 'k']);
        $coef = CoeficienteCustoTransporte::updateOrCreate(
            [
                'data_base' => $data['data_base'],
                'desoneracao' => $data['desoneracao'],
                'custo_transporte_id' => $data['custo_transporte_id']
            ],
            $data
        );
        return response()->json($coef);
    }

    public function storeLote(Request $request)
    {
        $data_base = $request->input('data_base');
        $desoneracao = $request->input('desoneracao');
        $itens = $request->input('coeficientes', []);
        if (!$data_base || !$desoneracao) {
            return response()->json(['error' => 'Data base e desoneração obrigatórios'], 422);
        }
        $ids = CustoTransporte::pluck('id')->toArray();
        foreach ($ids as $id) {
            $item = collect($itens)->firstWhere('custo_transporte_id', $id);
            $dados = [
                'data_base' => $data_base,
                'desoneracao' => $desoneracao,
                'custo_transporte_id' => $id,
                'x1' => $item['x1'] ?? 0,
                'x2' => $item['x2'] ?? 0,
                'k' => $item['k'] ?? 0,
            ];
            \App\Models\Transportes\CoeficienteCustoTransporte::updateOrCreate(
                [
                    'data_base' => $data_base,
                    'desoneracao' => $desoneracao,
                    'custo_transporte_id' => $id
                ],
                $dados
            );
        }
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $coef = CoeficienteCustoTransporte::findOrFail($id);
        $coef->delete();
        return response()->json(['success' => true]);
    }
} 