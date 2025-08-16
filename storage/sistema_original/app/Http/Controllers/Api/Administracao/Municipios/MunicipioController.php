<?php

namespace App\Http\Controllers\Api\Administracao\Municipios;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MunicipioController extends Controller
{
    /**
     * Lista registros com filtros e paginação
     */
    public function listar(Request $request)
    {
        try {
            $query = Municipio::query();

            // Aplicar filtros
            if ($request->has('nome') && !empty($request->nome)) {
                $query->where('nome', 'like', '%' . $request->nome . '%');
            }

            if ($request->has('prefeito') && !empty($request->prefeito)) {
                $query->where('prefeito', 'like', '%' . $request->prefeito . '%');
            }

            $registros = $query->paginate(15);
            
            Log::info('Listagem de municípios realizada', [
                'filtros' => $request->only(['nome', 'prefeito']),
                'total' => $registros->total()
            ]);

            return response()->json($registros);
        } catch (\Exception $e) {
            Log::error('Erro ao listar municípios', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Cria um novo registro
     */
    public function store(Request $request)
    {
        try {
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
                Log::warning('Validação falhou ao criar município', ['errors' => $validator->errors()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $registro = Municipio::create($request->all());
            
            Log::info('Município criado com sucesso', [
                'id' => $registro->id,
                'nome' => $registro->nome,
                'codigo_ibge' => $registro->codigo_ibge
            ]);

            return response()->json($registro, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar município', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Atualiza um registro existente
     */
    public function update(Request $request, $id)
    {
        try {
            $registro = Municipio::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nome' => 'required|string|max:255',
                'prefeito' => 'required|string|max:255',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('municipios')->ignore($registro->id)
                ],
                'endereco_prefeitura' => 'required|string|max:255',
                'codigo_ibge' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('municipios')->ignore($registro->id)
                ],
                'populacao' => 'required|integer|min:0',
                'cep' => 'required|string|max:20',
                'telefone' => 'required|string|max:20',
                'cnpj' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('municipios')->ignore($registro->id)
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
                Log::warning('Validação falhou ao atualizar município', ['id' => $id, 'errors' => $validator->errors()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $registro->update($request->all());
            
            Log::info('Município atualizado com sucesso', [
                'id' => $registro->id,
                'nome' => $registro->nome,
                'codigo_ibge' => $registro->codigo_ibge
            ]);

            return response()->json($registro);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar município', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Remove um registro
     */
    public function destroy($id)
    {
        try {
            $registro = Municipio::findOrFail($id);
            $nome = $registro->nome;
            $codigo_ibge = $registro->codigo_ibge;
            
            $registro->delete();
            
            Log::info('Município removido com sucesso', [
                'id' => $id,
                'nome' => $nome,
                'codigo_ibge' => $codigo_ibge
            ]);

            return response()->json(['message' => 'Município excluído com sucesso']);
        } catch (\Exception $e) {
            Log::error('Erro ao remover município', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Importa municípios em massa
     */
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

            $novos = 0;
            $atualizados = 0;

            foreach ($municipiosOrigem as $municipioOrigem) {
                $municipio = Municipio::updateOrCreate(
                    ['codigo_ibge' => $municipioOrigem->codigo_ibge],
                    [
                        'nome' => $municipioOrigem->nome,
                        'prefeito' => $municipioOrigem->prefeito,
                        'email' => $municipioOrigem->email,
                        'endereco_prefeitura' => $municipioOrigem->endereco_prefeitura,
                        'populacao' => $municipioOrigem->populacao,
                        'cep' => $municipioOrigem->cep,
                        'telefone' => $municipioOrigem->telefone,
                        'cnpj' => $municipioOrigem->cnpj,
                    ]
                );

                if ($municipio->wasRecentlyCreated) {
                    $novos++;
                } else {
                    $atualizados++;
                }
            }

            DB::commit();

            Log::info('Importação de municípios concluída com sucesso', [
                'novos' => $novos,
                'atualizados' => $atualizados,
                'total' => $municipiosOrigem->count()
            ]);

            return response()->json([
                'message' => 'Importação concluída com sucesso',
                'novos' => $novos,
                'atualizados' => $atualizados
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erro na importação de municípios', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro na importação'], 500);
        }
    }
}
