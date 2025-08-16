<?php

namespace App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias;

use App\Http\Controllers\Controller;
use App\Models\Gerais\EntidadeOrcamentaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EntidadeOrcamentariaController extends Controller
{
    /**
     * Lista registros com filtros e paginação
     */
    public function listar(Request $request)
    {
        try {
            $query = EntidadeOrcamentaria::query();

            // Aplicar filtros
            if ($request->has('razao_social') && !empty($request->razao_social)) {
                $query->where('razao_social', 'like', '%' . $request->razao_social . '%');
            }

            if ($request->has('nome_fantasia') && !empty($request->nome_fantasia)) {
                $query->where('nome_fantasia', 'like', '%' . $request->nome_fantasia . '%');
            }

            if ($request->has('tipo_organizacao') && !empty($request->tipo_organizacao)) {
                $query->where('tipo_organizacao', $request->tipo_organizacao);
            }

            // Ordenação padrão
            $query->orderBy('razao_social', 'asc');

            $registros = $query->paginate(15);
            
            Log::info('Listagem de entidades orçamentárias realizada', [
                'filtros' => $request->only(['razao_social', 'nome_fantasia', 'tipo_organizacao']),
                'total' => $registros->total()
            ]);

            return response()->json($registros);
        } catch (\Exception $e) {
            Log::error('Erro ao listar entidades orçamentárias', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Retorna apenas id e nome_fantasia como nome para campos de seleção
     */
    public function listarSelect()
    {
        try {
            $result = EntidadeOrcamentaria::select('id', 'nome_fantasia as nome')
                ->orderBy('nome_fantasia', 'asc')
                ->get();
                
            Log::info('Listagem de entidades orçamentárias para select realizada', [
                'total' => $result->count()
            ]);
            
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar entidades orçamentárias para select', ['error' => $e->getMessage()]);
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
                'razao_social' => 'required|string|max:255|unique:entidades_orcamentarias',
                'nome_fantasia' => 'required|string|max:255|unique:entidades_orcamentarias',
                'tipo_organizacao' => 'required|in:municipio,secretaria,órgão,autarquia,outros',
                'email' => 'required|email|max:255|unique:entidades_orcamentarias',
                'endereco' => 'nullable|string|max:255',
                'codigo_ibge' => 'nullable|string|max:20|unique:entidades_orcamentarias',
                'populacao' => 'nullable|integer',
                'cep' => 'nullable|string|max:10',
                'telefone' => 'required|string|max:20',
                'cnpj' => 'nullable|string|max:20|unique:entidades_orcamentarias',
                'responsavel' => 'required|string|max:255',
                'responsavel_cargo' => 'required|string|max:100',
                'responsavel_telefone' => 'nullable|string|max:20',
                'responsavel_email' => 'nullable|email|max:100'
            ], [
                'razao_social.required' => 'A razão social é obrigatória',
                'razao_social.unique' => 'Esta razão social já está cadastrada',
                'nome_fantasia.required' => 'O nome fantasia é obrigatório',
                'nome_fantasia.unique' => 'Este nome fantasia já está cadastrado',
                'tipo_organizacao.required' => 'O tipo de organização é obrigatório',
                'tipo_organizacao.in' => 'O tipo de organização selecionado é inválido',
                'email.required' => 'O email é obrigatório',
                'email.email' => 'O email informado é inválido',
                'email.unique' => 'Este email já está cadastrado',
                'codigo_ibge.unique' => 'Este código IBGE já está cadastrado',
                'populacao.integer' => 'A população deve ser um número inteiro',
                'telefone.required' => 'O telefone é obrigatório',
                'cnpj.unique' => 'Este CNPJ já está cadastrado',
                'responsavel.required' => 'O responsável é obrigatório',
                'responsavel_cargo.required' => 'O cargo do responsável é obrigatório',
                'responsavel_email.email' => 'O email do responsável é inválido'
            ]);

            if ($validator->fails()) {
                Log::warning('Validação falhou ao criar entidade orçamentária', ['errors' => $validator->errors()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $registro = EntidadeOrcamentaria::create($request->all());
            
            Log::info('Entidade orçamentária criada com sucesso', [
                'id' => $registro->id,
                'razao_social' => $registro->razao_social,
                'nome_fantasia' => $registro->nome_fantasia
            ]);

            return response()->json($registro, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar entidade orçamentária', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Atualiza um registro existente
     */
    public function update(Request $request, $id)
    {
        try {
            $registro = EntidadeOrcamentaria::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'razao_social' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('entidades_orcamentarias')->ignore($registro->id)
                ],
                'nome_fantasia' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('entidades_orcamentarias')->ignore($registro->id)
                ],
                'tipo_organizacao' => 'required|in:municipio,secretaria,órgão,autarquia,outros',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('entidades_orcamentarias')->ignore($registro->id)
                ],
                'endereco' => 'nullable|string|max:255',
                'codigo_ibge' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('entidades_orcamentarias')->ignore($registro->id)
                ],
                'populacao' => 'nullable|integer',
                'cep' => 'nullable|string|max:10',
                'telefone' => 'required|string|max:20',
                'cnpj' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('entidades_orcamentarias')->ignore($registro->id)
                ],
                'responsavel' => 'required|string|max:255',
                'responsavel_cargo' => 'required|string|max:100',
                'responsavel_telefone' => 'nullable|string|max:20',
                'responsavel_email' => 'nullable|email|max:100'
            ], [
                'razao_social.required' => 'A razão social é obrigatória',
                'razao_social.unique' => 'Esta razão social já está cadastrada',
                'nome_fantasia.required' => 'O nome fantasia é obrigatório',
                'nome_fantasia.unique' => 'Este nome fantasia já está cadastrado',
                'tipo_organizacao.required' => 'O tipo de organização é obrigatório',
                'tipo_organizacao.in' => 'O tipo de organização selecionado é inválido',
                'email.required' => 'O email é obrigatório',
                'email.email' => 'O email informado é inválido',
                'email.unique' => 'Este email já está cadastrado',
                'codigo_ibge.unique' => 'Este código IBGE já está cadastrado',
                'populacao.integer' => 'A população deve ser um número inteiro',
                'telefone.required' => 'O telefone é obrigatório',
                'cnpj.unique' => 'Este CNPJ já está cadastrado',
                'responsavel.required' => 'O responsável é obrigatório',
                'responsavel_cargo.required' => 'O cargo do responsável é obrigatório',
                'responsavel_email.email' => 'O email do responsável é inválido'
            ]);

            if ($validator->fails()) {
                Log::warning('Validação falhou ao atualizar entidade orçamentária', ['id' => $id, 'errors' => $validator->errors()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $registro->update($request->all());
            
            Log::info('Entidade orçamentária atualizada com sucesso', [
                'id' => $registro->id,
                'razao_social' => $registro->razao_social,
                'nome_fantasia' => $registro->nome_fantasia
            ]);

            return response()->json($registro);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar entidade orçamentária', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Remove um registro
     */
    public function destroy($id)
    {
        try {
            $registro = EntidadeOrcamentaria::findOrFail($id);
            $razao_social = $registro->razao_social;
            $nome_fantasia = $registro->nome_fantasia;
            
            $registro->delete();
            
            Log::info('Entidade orçamentária removida com sucesso', [
                'id' => $id,
                'razao_social' => $razao_social,
                'nome_fantasia' => $nome_fantasia
            ]);

            return response()->json(['message' => 'Entidade orçamentária excluída com sucesso']);
        } catch (\Exception $e) {
            Log::error('Erro ao remover entidade orçamentária', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Importa municípios como entidades orçamentárias
     */
    public function importarMunicipios()
    {
        try {
            DB::beginTransaction();

            $municipios = \App\Models\Municipio::all();
            $importados = 0;
            $ignorados = 0;

            foreach ($municipios as $municipio) {
                // Verifica se já existe uma entidade com este CNPJ ou código IBGE
                $existe = EntidadeOrcamentaria::where('cnpj', $municipio->cnpj)
                    ->orWhere('codigo_ibge', $municipio->codigo_ibge)
                    ->exists();

                if (!$existe) {
                    // Verifica se o município tem email, caso contrário usa email padrão
                    $email = $municipio->email ?: 'municipio.' . strtolower(str_replace(' ', '', $municipio->nome)) . '@exemplo.com';
                    
                    EntidadeOrcamentaria::create([
                        'razao_social' => $municipio->nome,
                        'nome_fantasia' => $municipio->nome,
                        'tipo_organizacao' => 'municipio',
                        'email' => $email,
                        'endereco' => $municipio->endereco_prefeitura,
                        'codigo_ibge' => $municipio->codigo_ibge,
                        'populacao' => $municipio->populacao,
                        'cep' => $municipio->cep,
                        'telefone' => $municipio->telefone,
                        'cnpj' => $municipio->cnpj,
                        'responsavel' => $municipio->prefeito,
                        'responsavel_cargo' => 'Prefeito',
                        'responsavel_telefone' => '',
                        'responsavel_email' => '',
                    ]);
                    $importados++;
                } else {
                    $ignorados++;
                }
            }

            DB::commit();

            Log::info('Importação de municípios concluída com sucesso', [
                'importados' => $importados,
                'ignorados' => $ignorados,
                'total' => $municipios->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Importação concluída! {$importados} municípios importados, {$ignorados} já existentes."
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erro na importação de municípios', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro na importação'], 500);
        }
    }
}
