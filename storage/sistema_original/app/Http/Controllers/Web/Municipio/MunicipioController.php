<?php

namespace App\Http\Controllers\Web\Municipio;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MunicipioController extends Controller
{
    public function index()
    {
        return view('municipios.index');
    }

    public function create()
    {
        return view('municipios.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'prefeito' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:municipios',
            'endereco_prefeitura' => 'required|string|max:255',
            'codigo_ibge' => 'required|string|max:20|unique:municipios',
            'populacao' => 'required|integer|min:0',
            'cep' => 'required|string|max:20',
            'telefone' => 'required|string|max:20',
            'cnpj' => 'required|string|max:20|unique:municipios',
        ], [
            'nome.required' => 'O nome do município é obrigatório',
            'prefeito.required' => 'O nome do prefeito é obrigatório',
            'email.email' => 'O email informado é inválido',
            'email.unique' => 'Este email já está cadastrado',
            'endereco_prefeitura.required' => 'O endereço da prefeitura é obrigatório',
            'codigo_ibge.required' => 'O código IBGE é obrigatório',
            'codigo_ibge.unique' => 'Este código IBGE já está cadastrado',
            'populacao.required' => 'A população é obrigatória',
            'populacao.min' => 'A população não pode ser negativa',
            'cep.required' => 'O CEP é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'cnpj.required' => 'O CNPJ é obrigatório',
            'cnpj.unique' => 'Este CNPJ já está cadastrado',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $municipio = Municipio::create($request->all());
        return response()->json($municipio, 201);
    }

    public function edit($id)
    {
        $municipio = Municipio::findOrFail($id);
        return view('municipios.edit', compact('municipio'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'prefeito' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('municipios')->ignore($id)
            ],
            'endereco_prefeitura' => 'required|string|max:255',
            'codigo_ibge' => [
                'required',
                'string',
                'max:20',
                Rule::unique('municipios')->ignore($id)
            ],
            'populacao' => 'required|integer|min:0',
            'cep' => 'required|string|max:20',
            'telefone' => 'required|string|max:20',
            'cnpj' => [
                'required',
                'string',
                'max:20',
                Rule::unique('municipios')->ignore($id)
            ],
        ], [
            'nome.required' => 'O nome do município é obrigatório',
            'prefeito.required' => 'O nome do prefeito é obrigatório',
            'email.email' => 'O email informado é inválido',
            'email.unique' => 'Este email já está cadastrado',
            'endereco_prefeitura.required' => 'O endereço da prefeitura é obrigatório',
            'codigo_ibge.required' => 'O código IBGE é obrigatório',
            'codigo_ibge.unique' => 'Este código IBGE já está cadastrado',
            'populacao.required' => 'A população é obrigatória',
            'populacao.min' => 'A população não pode ser negativa',
            'cep.required' => 'O CEP é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'cnpj.required' => 'O CNPJ é obrigatório',
            'cnpj.unique' => 'Este CNPJ já está cadastrado',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $municipio = Municipio::findOrFail($id);
        $municipio->update($request->all());
        return response()->json($municipio);
    }

    public function destroy($id)
    {
        $municipio = Municipio::findOrFail($id);
        $municipio->delete();
        return response()->json(null, 204);
    }

    public function listar(Request $request)
    {
        $query = Municipio::query();

        // Filtro por nome
        if ($request->has('nome') && !empty($request->nome)) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        // Filtro por prefeito
        if ($request->has('prefeito') && !empty($request->prefeito)) {
            $query->where('prefeito', 'like', '%' . $request->prefeito . '%');
        }

        $municipios = $query->paginate(100);
        return response()->json($municipios);
    }

    public function importar()
    {
        try {
            DB::beginTransaction();

            // Busca os municípios da tabela de origem (PostgreSQL)
            $municipiosOrigem = DB::connection('pgsql')
                ->table('municipio')
                ->select([
                    'nome',
                    'prefeito',
                    'email',
                    'endereco_prefeitura',
                    'codigo_ibge',
                    'populacao',
                    'cep',
                    'telefone',
                    'cnpj'
                ])
                ->get();

            $importados = 0;
            $atualizados = 0;
            foreach ($municipiosOrigem as $municipio) {
                // Busca por nome (case-insensitive)
                $municipioExistente = Municipio::whereRaw('LOWER(nome) = ?', [mb_strtolower($municipio->nome)])->first();
                if ($municipioExistente) {
                    $municipioExistente->update([
                        'prefeito' => $municipio->prefeito ?? '',
                        'email' => $municipio->email ?? null,
                        'endereco_prefeitura' => $municipio->endereco_prefeitura ?? '',
                        'codigo_ibge' => $municipio->codigo_ibge,
                        'populacao' => $municipio->populacao ?? 0,
                        'cep' => $municipio->cep ?? '',
                        'telefone' => $municipio->telefone ?? '',
                        'cnpj' => $municipio->cnpj ?? '',
                    ]);
                    $atualizados++;
                } else {
                    Municipio::create([
                        'nome' => $municipio->nome,
                        'prefeito' => $municipio->prefeito ?? '',
                        'email' => $municipio->email ?? null,
                        'endereco_prefeitura' => $municipio->endereco_prefeitura ?? '',
                        'codigo_ibge' => $municipio->codigo_ibge,
                        'populacao' => $municipio->populacao ?? 0,
                        'cep' => $municipio->cep ?? '',
                        'telefone' => $municipio->telefone ?? '',
                        'cnpj' => $municipio->cnpj ?? '',
                    ]);
                    $importados++;
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Importação concluída com sucesso',
                'novos' => $importados,
                'atualizados' => $atualizados
            ]);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json([
                'message' => 'Erro ao importar municípios: ' . $e->getMessage()
            ], 500);
        }
    }
} 