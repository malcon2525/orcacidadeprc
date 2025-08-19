<?php

namespace App\Http\Controllers\Api\Administracao\Municipios;

use App\Http\Controllers\Controller;
use App\Models\Administracao\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MunicipioController extends Controller
{
    /**
     * Método index que chama listar (para compatibilidade com apiResource)
     */
    public function index(Request $request)
    {
        return $this->listar($request);
    }

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
                Log::warning('Validação falhou ao atualizar município', ['errors' => $validator->errors()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $registro = Municipio::findOrFail($id);
            $registro->update($request->all());
            
            Log::info('Município atualizado com sucesso', [
                'id' => $registro->id,
                'nome' => $registro->nome
            ]);

            return response()->json($registro);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar município', ['error' => $e->getMessage()]);
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
            $registro->delete();
            
            Log::info('Município removido com sucesso', [
                'id' => $id
            ]);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Erro ao remover município', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Importa municípios de fonte externa
     */
    public function importar()
    {
        try {
            // Verificar se a conexão PostgreSQL está configurada
            if (!config('database.connections.pgsql.host') || config('database.connections.pgsql.host') === 'nada') {
                return response()->json([
                    'message' => 'Conexão PostgreSQL não configurada. Configure as variáveis de ambiente DB_PG_* no arquivo .env',
                    'error' => 'CONNECTION_NOT_CONFIGURED'
                ], 400);
            }

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

            Log::info('Importação de municípios concluída', [
                'novos' => $importados,
                'atualizados' => $atualizados
            ]);

            return response()->json([
                'message' => 'Importação concluída com sucesso',
                'novos' => $importados,
                'atualizados' => $atualizados
            ]);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            Log::error('Erro ao importar municípios', ['error' => $e->getMessage()]);
            
            // Verificar se é erro de conexão
            if (str_contains($e->getMessage(), 'SSL negotiation') || str_contains($e->getMessage(), 'Connection refused')) {
                return response()->json([
                    'message' => 'Erro de conexão com PostgreSQL. Verifique se o banco está rodando e as configurações estão corretas.',
                    'error' => 'CONNECTION_ERROR',
                    'details' => $e->getMessage()
                ], 500);
            }
            
            return response()->json([
                'message' => 'Erro ao importar municípios: ' . $e->getMessage()
            ], 500);
        }
    }
}
