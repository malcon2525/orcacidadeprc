<?php

namespace App\Http\Controllers\Web\Gerais;

use App\Http\Controllers\Controller;
use App\Models\Gerais\EntidadeOrcamentaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntidadeOrcamentariaController extends Controller
{
    /**
     * Exibe a listagem do recurso
     */
    public function index()
    {
        return view('gerais.entidades-orcamentarias.index');
    }

    /**
     * Retorna os dados para a listagem
     */
    public function listar(Request $request)
    {
        Log::info('Iniciando listagem de entidades orçamentárias');
        
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

        // Paginação
        $result = $query->paginate(100);
        
        Log::info('Dados encontrados:', ['total' => $result->total()]);
        
        return response()->json($result);
    }

    /**
     * Retorna apenas id e nome_fantasia como nome para campos de seleção
     * Este método é específico para preencher campos select/dropdown
     */
    public function listarSelect()
    {
        Log::info('Iniciando listagem de entidades orçamentárias para select');
        
        $result = EntidadeOrcamentaria::select('id', 'nome_fantasia as nome')
            ->orderBy('nome_fantasia', 'asc')
            ->get();
            
        Log::info('Dados encontrados para select:', ['total' => $result->count()]);
        
        return response()->json(['data' => $result]);
    }

    /**
     * Armazena um novo recurso
     */
    public function store(Request $request)
    {
        Log::info('Iniciando criação de entidade orçamentária', $request->all());
        
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres',
            'email' => 'O campo :attribute deve ser um e-mail válido',
            'unique' => 'Este :attribute já está cadastrado',
            'integer' => 'O campo :attribute deve ser um número inteiro',
            'in' => 'O valor selecionado para :attribute é inválido'
        ];

        $atributos = [
            'razao_social' => 'razão social',
            'nome_fantasia' => 'nome fantasia',
            'tipo_organizacao' => 'tipo de organização',
            'email' => 'e-mail',
            'endereco' => 'endereço',
            'codigo_ibge' => 'código IBGE',
            'populacao' => 'população',
            'cep' => 'CEP',
            'telefone' => 'telefone',
            'cnpj' => 'CNPJ',
            'responsavel' => 'responsável',
            'responsavel_cargo' => 'cargo do responsável',
            'responsavel_telefone' => 'telefone do responsável',
            'responsavel_email' => 'e-mail do responsável'
        ];

        $validated = $request->validate([
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
        ], $mensagens, $atributos);

        $entidade = EntidadeOrcamentaria::create($validated);
        
        Log::info('Entidade criada com sucesso', ['id' => $entidade->id]);

        return response()->json($entidade, 201);
    }

    /**
     * Atualiza um recurso existente
     */
    public function update(Request $request, $id)
    {
        Log::info('Iniciando atualização de entidade orçamentária', ['id' => $id, 'dados' => $request->all()]);
        
        $mensagens = [
            'required' => 'O campo :attribute é obrigatório',
            'string' => 'O campo :attribute deve ser um texto',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres',
            'email' => 'O campo :attribute deve ser um e-mail válido',
            'unique' => 'Este :attribute já está cadastrado',
            'integer' => 'O campo :attribute deve ser um número inteiro',
            'in' => 'O valor selecionado para :attribute é inválido'
        ];

        $atributos = [
            'razao_social' => 'razão social',
            'nome_fantasia' => 'nome fantasia',
            'tipo_organizacao' => 'tipo de organização',
            'email' => 'e-mail',
            'endereco' => 'endereço',
            'codigo_ibge' => 'código IBGE',
            'populacao' => 'população',
            'cep' => 'CEP',
            'telefone' => 'telefone',
            'cnpj' => 'CNPJ',
            'responsavel' => 'responsável',
            'responsavel_cargo' => 'cargo do responsável',
            'responsavel_telefone' => 'telefone do responsável',
            'responsavel_email' => 'e-mail do responsável'
        ];

        $entidade = EntidadeOrcamentaria::findOrFail($id);

        $validated = $request->validate([
            'razao_social' => 'required|string|max:255|unique:entidades_orcamentarias,razao_social,' . $id,
            'nome_fantasia' => 'required|string|max:255|unique:entidades_orcamentarias,nome_fantasia,' . $id,
            'tipo_organizacao' => 'required|in:municipio,secretaria,órgão,autarquia,outros',
            'email' => 'required|email|max:255|unique:entidades_orcamentarias,email,' . $id,
            'endereco' => 'nullable|string|max:255',
            'codigo_ibge' => 'nullable|string|max:20|unique:entidades_orcamentarias,codigo_ibge,' . $id,
            'populacao' => 'nullable|integer',
            'cep' => 'nullable|string|max:10',
            'telefone' => 'required|string|max:20',
            'cnpj' => 'nullable|string|max:20|unique:entidades_orcamentarias,cnpj,' . $id,
            'responsavel' => 'required|string|max:255',
            'responsavel_cargo' => 'required|string|max:100',
            'responsavel_telefone' => 'nullable|string|max:20',
            'responsavel_email' => 'nullable|email|max:100'
        ], $mensagens, $atributos);

        $entidade->update($validated);
        
        Log::info('Entidade atualizada com sucesso', ['id' => $id]);

        return response()->json($entidade);
    }

    /**
     * Remove um recurso
     */
    public function destroy($id)
    {
        Log::info('Iniciando exclusão de entidade orçamentária', ['id' => $id]);
        
        $entidade = EntidadeOrcamentaria::findOrFail($id);
        $entidade->delete();
        
        Log::info('Entidade excluída com sucesso', ['id' => $id]);

        return response()->json(null, 204);
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
                    EntidadeOrcamentaria::create([
                        'razao_social' => $municipio->nome,
                        'nome_fantasia' => $municipio->nome,
                        'tipo_organizacao' => 'municipio',
                        'email' => $municipio->email,
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

            return response()->json([
                'success' => true,
                'message' => "Importação concluída! {$importados} municípios importados, {$ignorados} já existentes."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar municípios: ' . $e->getMessage()
            ], 500);
        }
    }
} 