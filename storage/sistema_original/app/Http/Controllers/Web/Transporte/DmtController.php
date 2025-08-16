<?php

namespace App\Http\Controllers\Web\Transporte;

use App\Http\Controllers\Controller;
use App\Models\Transportes\Dmt;
use App\Models\Municipio;
use App\Models\Gerais\EntidadeOrcamentaria;
use Illuminate\Http\Request;

class DmtController extends Controller
{
    public function index()
    {
        return view('transporte.dmt.index');
    }

    public function listar(Request $request)
    {
        $query = Dmt::with(['municipio', 'entidadeOrcamentaria']);

        if ($request->has('destino') && !empty($request->destino)) {
            $query->where('destino', 'like', '%' . $request->destino . '%');
        }
        if ($request->has('codigo_material') && !empty($request->codigo_material)) {
            $query->where('codigo_material', 'like', '%' . $request->codigo_material . '%');
        }
        if ($request->has('nome_material') && !empty($request->nome_material)) {
            $query->where('nome_material', 'like', '%' . $request->nome_material . '%');
        }
        if ($request->has('tipo') && !empty($request->tipo)) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->has('id_municipio') && !empty($request->id_municipio)) {
            $query->where('id_municipio', $request->id_municipio);
        }
        if ($request->has('id_entidade_orcamentaria') && !empty($request->id_entidade_orcamentaria)) {
            $query->where('id_entidade_orcamentaria', $request->id_entidade_orcamentaria);
        }

        $query->orderBy('destino', 'asc')->orderBy('codigo_material', 'asc');
        $result = $query->get()->map(function($item) {
            $arr = $item->toArray();
            if ($item->entidadeOrcamentaria) {
                $arr['entidade_orcamentaria'] = [
                    'id' => $item->entidadeOrcamentaria->id,
                    'nome' => $item->entidadeOrcamentaria->razao_social
                ];
            } else {
                $arr['entidade_orcamentaria'] = null;
            }
            return $arr;
        });
        return response()->json([
            'data' => $result,
            'current_page' => 1,
            'from' => count($result) > 0 ? 1 : 0,
            'to' => count($result),
            'total' => count($result),
            'last_page' => 1
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_material' => 'required|string|max:10',
            'nome_material' => 'required|string|max:255',
            'origem' => 'nullable|string|max:150',
            'destino' => 'nullable|string|max:150',
            'sigla_transporte' => 'required|string|max:3',
            'tipo' => 'required|in:local,comercial',
            'x1' => 'required|numeric',
            'x2' => 'required|numeric',
            'id_municipio' => 'nullable|exists:municipios,id',
            'id_entidade_orcamentaria' => 'required|exists:entidades_orcamentarias,id',
        ]);
        $item = Dmt::create($validated);
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = Dmt::findOrFail($id);
        $validated = $request->validate([
            'codigo_material' => 'required|string|max:10',
            'nome_material' => 'required|string|max:255',
            'origem' => 'nullable|string|max:150',
            'destino' => 'nullable|string|max:150',
            'sigla_transporte' => 'required|string|max:3',
            'tipo' => 'required|in:local,comercial',
            'x1' => 'required|numeric',
            'x2' => 'required|numeric',
            'id_municipio' => 'nullable|exists:municipios,id',
            'id_entidade_orcamentaria' => 'required|exists:entidades_orcamentarias,id',
        ]);
        $item->update($validated);
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = Dmt::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }

    public function selects()
    {
        $municipios = Municipio::orderBy('nome')->get(['id', 'nome']);
        $entidades = EntidadeOrcamentaria::orderBy('razao_social')->get(['id', 'razao_social as nome']);
        return response()->json([
            'municipios' => $municipios,
            'entidades_orcamentarias' => $entidades,
        ]);
    }

    public function gerarDmtsParaEntidadeOrcamentaria(Request $request)
    {
        $request->validate([
            'id_entidade_orcamentaria' => 'required|exists:entidades_orcamentarias,id',
            'id_municipio' => 'nullable|exists:municipios,id',
        ]);
        $idEntidade = $request->id_entidade_orcamentaria;
        $idMunicipio = $request->id_municipio;

        $materiais = \App\Models\Transportes\DmtDefault::all();
        $total = 0;
        foreach ($materiais as $mat) {
            $data = [
                'codigo_material' => $mat->codigo_material,
                'nome_material' => $mat->nome_material,
                'origem' => $mat->origem,
                'destino' => $mat->destino,
                'sigla_transporte' => $mat->sigla_transporte,
                'tipo' => $mat->tipo,
                'x1' => $mat->x1,
                'x2' => $mat->x2,
                'id_entidade_orcamentaria' => $idEntidade,
                'id_municipio' => $idMunicipio,
            ];
            $dmt = \App\Models\Transportes\Dmt::where('codigo_material', $data['codigo_material'])
                ->where('nome_material', $data['nome_material'])
                ->where('destino', $data['destino'])
                ->where('sigla_transporte', $data['sigla_transporte'])
                ->where('id_entidade_orcamentaria', $idEntidade)
                ->where(function($q) use ($idMunicipio) {
                    if ($idMunicipio) {
                        $q->where('id_municipio', $idMunicipio);
                    } else {
                        $q->whereNull('id_municipio');
                    }
                })
                ->first();
            if ($dmt) {
                $dmt->update([
                    'origem' => $mat->origem,
                    'x1' => $mat->x1,
                    'x2' => $mat->x2,
                    'tipo' => $mat->tipo,
                ]);
            } else {
                \App\Models\Transportes\Dmt::create($data);
            }
            $total++;
        }
        return response()->json(['success' => true, 'total' => $total]);
    }

    public function atualizarEmLote(Request $request)
    {
        $dados = $request->all();
        if (!is_array($dados)) {
            return response()->json(['error' => 'Formato inválido.'], 400);
        }
        $ids = collect($dados)->pluck('id')->filter()->all();
        $dmts = Dmt::whereIn('id', $ids)->get()->keyBy('id');
        $atualizados = 0;
        foreach ($dados as $item) {
            if (!isset($item['id']) || !isset($dmts[$item['id']])) continue;
            $dmt = $dmts[$item['id']];
            $dmt->origem = $item['origem'] ?? $dmt->origem;
            $dmt->x1 = is_numeric($item['x1']) ? $item['x1'] : $dmt->x1;
            $dmt->x2 = is_numeric($item['x2']) ? $item['x2'] : $dmt->x2;
            $dmt->save();
            $atualizados++;
        }
        return response()->json(['success' => true, 'atualizados' => $atualizados]);
    }
} 