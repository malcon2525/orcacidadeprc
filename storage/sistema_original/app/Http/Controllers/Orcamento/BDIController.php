<?php

namespace App\Http\Controllers\Orcamento;

use App\Http\Controllers\Controller;
use App\Models\Orcamento\BDI;
use App\Models\Gerais\EntidadeOrcamentaria;
use App\Models\Orcamento\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BDIController extends Controller
{
    public function index()
    {
        return view('orcamento.bdi.index');
    }

    public function listar(Request $request)
    {
        $query = BDI::with(['entidadeOrcamentaria' => function($q) {
            $q->select('id', 'nome_fantasia as nome');
        }])
            ->select('bdis.*');

        // Filtros
        if ($request->has('nome') && !empty($request->nome)) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }
        if ($request->has('entidade_orcamentaria_id') && !empty($request->entidade_orcamentaria_id)) {
            $query->where('entidade_orcamentaria_id', $request->entidade_orcamentaria_id);
        }

        $query->orderBy('id', 'desc');
        $result = $query->paginate(50);
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'nullable|string|max:255',
            'origem' => 'required|in:entidade_orcamentaria,orcamento',
            'orcamento_id' => 'nullable|exists:orcamentos,id',
            'entidade_orcamentaria_id' => 'nullable|exists:entidades_orcamentarias,id',
            'adm_central_servico' => 'numeric',
            'riscos_servico' => 'numeric',
            'seguros_garantia_servico' => 'numeric',
            'desp_financeira_servico' => 'numeric',
            'lucro_servico' => 'numeric',
            'adm_central_material' => 'numeric',
            'riscos_material' => 'numeric',
            'seguros_garantia_material' => 'numeric',
            'desp_financeira_material' => 'numeric',
            'lucro_material' => 'numeric',
            'adm_central_equipamento' => 'numeric',
            'riscos_equipamento' => 'numeric',
            'seguros_garantia_equipamento' => 'numeric',
            'desp_financeira_equipamento' => 'numeric',
            'lucro_equipamento' => 'numeric',
            'iss_municipio' => 'numeric',
            'base_calculo_mao_obra' => 'numeric',
            'iss_calculado' => 'numeric',
            'pis' => 'numeric',
            'cofins' => 'numeric',
            'cprb' => 'numeric',
            'impostos_total' => 'numeric',
            'bdi_servico' => 'numeric',
            'bdi_material' => 'numeric',
            'bdi_equipamento' => 'numeric',
            'analisado' => 'boolean',
        ]);
        $validated['analisado'] = $request->input('analisado', false);
        $bdi = BDI::create($validated);
        return response()->json($bdi, 201);
    }

    public function update(Request $request, $id)
    {
        $bdi = BDI::findOrFail($id);
        $validated = $request->validate([
            'nome' => 'nullable|string|max:255',
            'origem' => 'required|in:entidade_orcamentaria,orcamento',
            'orcamento_id' => 'nullable|exists:orcamentos,id',
            'entidade_orcamentaria_id' => 'nullable|exists:entidades_orcamentarias,id',
            'adm_central_servico' => 'numeric',
            'riscos_servico' => 'numeric',
            'seguros_garantia_servico' => 'numeric',
            'desp_financeira_servico' => 'numeric',
            'lucro_servico' => 'numeric',
            'adm_central_material' => 'numeric',
            'riscos_material' => 'numeric',
            'seguros_garantia_material' => 'numeric',
            'desp_financeira_material' => 'numeric',
            'lucro_material' => 'numeric',
            'adm_central_equipamento' => 'numeric',
            'riscos_equipamento' => 'numeric',
            'seguros_garantia_equipamento' => 'numeric',
            'desp_financeira_equipamento' => 'numeric',
            'lucro_equipamento' => 'numeric',
            'iss_municipio' => 'numeric',
            'base_calculo_mao_obra' => 'numeric',
            'iss_calculado' => 'numeric',
            'pis' => 'numeric',
            'cofins' => 'numeric',
            'cprb' => 'numeric',
            'impostos_total' => 'numeric',
            'bdi_servico' => 'numeric',
            'bdi_material' => 'numeric',
            'bdi_equipamento' => 'numeric',
            'analisado' => 'boolean',
        ]);
        $bdi->update($validated);
        return response()->json($bdi);
    }

    public function destroy($id)
    {
        $bdi = BDI::findOrFail($id);
        $bdi->delete();
        return response()->json(null, 204);
    }
} 