<?php

namespace App\Http\Controllers\Api\Administracao\EntidadesOrcamentarias;

use App\Http\Controllers\Controller;
use App\Models\Administracao\EntidadesOrcamentarias\EntidadeOrcamentaria;
use App\Models\Administracao\Municipio;
use App\Services\Logging\EntidadesOrcamentariasLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EntidadeOrcamentariaController extends Controller
{
    protected $logger;
    
    public function __construct(EntidadesOrcamentariasLogService $logger)
    {
        $this->logger = $logger;
    }

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
            Log::info('=== LISTAR ENTIDADES - INÍCIO ===', [
                'user_id' => Auth::id(),
                'filtros' => $request->all(),
                'request_url' => $request->fullUrl()
            ]);

            $query = EntidadeOrcamentaria::query();

            // Aplicar filtros
            if ($request->has('razao_social') && !empty($request->razao_social)) {
                Log::info('Aplicando filtro razao_social', ['valor' => $request->razao_social]);
                $query->where('razao_social', 'like', '%' . $request->razao_social . '%');
            }

            if ($request->has('nome_fantasia') && !empty($request->nome_fantasia)) {
                Log::info('Aplicando filtro nome_fantasia', ['valor' => $request->nome_fantasia]);
                $query->where('nome_fantasia', 'like', '%' . $request->nome_fantasia . '%');
            }

            if ($request->has('tipo_organizacao') && !empty($request->tipo_organizacao)) {
                Log::info('Aplicando filtro tipo_organizacao', ['valor' => $request->tipo_organizacao]);
                $query->where('tipo_organizacao', $request->tipo_organizacao);
            }

            // Ordenação padrão
            $query->orderBy('razao_social', 'asc');

            Log::info('Executando paginação...');
            $registros = $query->paginate(15);
            
            Log::info('Resultados encontrados', [
                'total' => $registros->total(),
                'por_pagina' => $registros->perPage(),
                'pagina_atual' => $registros->currentPage()
            ]);
            
            return response()->json($registros);
        } catch (\Exception $e) {
            Log::error('=== ERRO EM LISTAR ENTIDADES ===', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'filtros' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->logger->erroCritico('LISTAGEM_ENTIDADES', $e->getMessage(), [
                'filtros' => $request->only(['razao_social', 'nome_fantasia', 'tipo_organizacao'])
            ]);
            
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
                
            // Retornar dados sem log desnecessário
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            $this->logger->erroCritico('LISTAGEM_SELECT', $e->getMessage());
            
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Cria um novo registro
     */
    public function store(Request $request)
    {
        try {
            Log::info('=== STORE ENTIDADE - INÍCIO ===', [
                'user_id' => Auth::id(),
                'dados_recebidos' => $request->all(),
                'request_url' => $request->fullUrl()
            ]);

            $validator = Validator::make($request->all(), [
                'razao_social' => 'required|string|max:255|unique:entidades_orcamentarias',
                'nome_fantasia' => 'required|string|max:255|unique:entidades_orcamentarias',
                'tipo_organizacao' => 'required|in:municipio,secretaria,órgão,autarquia,outros',
                'email' => 'required|email|max:255|unique:entidades_orcamentarias',
                'endereco' => 'nullable|string|max:255',
                'nivel_administrativo' => 'required|in:municipal,estadual,federal',
                'jurisdicao_nome' => 'required|string|max:255',
                'jurisdicao_codigo_ibge' => 'nullable|string|max:20',
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
                Log::warning('Validação falhou no store', ['errors' => $validator->errors()]);
                $this->logger->erroValidacao('CRIACAO_ENTIDADE', $validator->errors()->toArray(), [
                    'dados' => $request->only(['razao_social', 'nome_fantasia', 'tipo_organizacao'])
                ]);
                
                return response()->json(['errors' => $validator->errors()], 422);
            }

            Log::info('Validação passou, criando registro...');
            $registro = EntidadeOrcamentaria::create($request->all());
            
            Log::info('Registro criado com sucesso', ['id' => $registro->id]);
            
            $this->logger->sucessoCriacao($registro->id, $request->all(), [
                'criado_por' => Auth::id()
            ]);

            return response()->json($registro, 201);
        } catch (\Exception $e) {
            Log::error('=== ERRO EM STORE ENTIDADE ===', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'dados_enviados' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->logger->erroCritico('CRIACAO_ENTIDADE', $e->getMessage(), [
                'dados' => $request->all()
            ]);
            
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Exibe um registro específico
     */
    public function show($id)
    {
        try {
            $this->checkAccess(['gerenciar_entidades_orcamentarias']);

            $entidade = EntidadeOrcamentaria::findOrFail($id);

            return response()->json(['data' => $entidade]);
        } catch (\Exception $e) {
            $this->logger->erroCritico('VISUALIZACAO_ENTIDADE', $e->getMessage(), [
                'entidade_id' => $id,
                'visualizado_por' => Auth::id()
            ]);
            
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
                $this->logger->erroValidacao('EDICAO_ENTIDADE', $validator->errors()->toArray(), [
                    'entidade_id' => $id,
                    'dados' => $request->only(['razao_social', 'nome_fantasia', 'tipo_organizacao'])
                ]);
                
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $dadosAnteriores = $registro->toArray();
            $registro->update($request->all());
            
            $this->logger->sucessoEdicao($id, $request->all(), [
                'dados_anteriores' => $dadosAnteriores,
                'editado_por' => Auth::id()
            ]);

            return response()->json($registro);
        } catch (\Exception $e) {
            $this->logger->erroCritico('EDICAO_ENTIDADE', $e->getMessage(), [
                'entidade_id' => $id,
                'dados' => $request->all()
            ]);
            
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
            $dadosExcluidos = $registro->toArray();
            $registro->delete();
            
            $this->logger->sucessoExclusao($id, [
                'dados_excluidos' => $dadosExcluidos,
                'excluido_por' => Auth::id()
            ]);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            $this->logger->erroCritico('EXCLUSAO_ENTIDADE', $e->getMessage(), [
                'entidade_id' => $id
            ]);
            
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Importa municípios como entidades orçamentárias
     */
    public function importarMunicipios()
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
                $entidadeExistente = EntidadeOrcamentaria::whereRaw('LOWER(nome_fantasia) = ?', [mb_strtolower($municipio->nome)])->first();
                if ($entidadeExistente) {
                    $entidadeExistente->update([
                        'razao_social' => $municipio->nome,
                        'nome_fantasia' => $municipio->nome,
                        'tipo_organizacao' => 'municipio',
                        'email' => $municipio->email ?? '',
                        'endereco' => $municipio->endereco_prefeitura ?? '',
                        'codigo_ibge' => $municipio->codigo_ibge,
                        'populacao' => $municipio->populacao ?? 0,
                        'cep' => $municipio->cep ?? '',
                        'telefone' => $municipio->telefone ?? '',
                        'cnpj' => $municipio->cnpj ?? '',
                        'responsavel' => $municipio->prefeito ?? 'Prefeito',
                        'responsavel_cargo' => 'Prefeito',
                        'responsavel_telefone' => $municipio->telefone ?? '',
                        'responsavel_email' => $municipio->email ?? '',
                        'ativo' => true
                    ]);
                    $atualizados++;
                } else {
                    EntidadeOrcamentaria::create([
                        'razao_social' => $municipio->nome,
                        'nome_fantasia' => $municipio->nome,
                        'tipo_organizacao' => 'municipio',
                        'email' => $municipio->email ?? '',
                        'endereco' => $municipio->endereco_prefeitura ?? '',
                        'codigo_ibge' => $municipio->codigo_ibge,
                        'populacao' => $municipio->populacao ?? 0,
                        'cep' => $municipio->cep ?? '',
                        'telefone' => $municipio->telefone ?? '',
                        'cnpj' => $municipio->cnpj ?? '',
                        'responsavel' => $municipio->prefeito ?? 'Prefeito',
                        'responsavel_cargo' => 'Prefeito',
                        'responsavel_telefone' => $municipio->telefone ?? '',
                        'responsavel_email' => $municipio->email ?? '',
                        'ativo' => true
                    ]);
                    $importados++;
                }
            }

            DB::commit();

            $this->logger->sucessoImportacao($importados, $atualizados, [
                'importado_por' => Auth::id()
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
            
            $this->logger->erroCritico('IMPORTACAO_MUNICIPIOS', $e->getMessage(), [
                'importado_por' => Auth::id()
            ]);
            
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

    /**
     * Retorna municípios para seleção (filtrado por estado)
     */
    public function buscarMunicipios(Request $request)
    {
        try {
            Log::info('=== BUSCAR MUNICÍPIOS - INÍCIO ===', [
                'user_id' => Auth::id(),
                'estado' => $request->estado,
                'request_url' => $request->fullUrl()
            ]);

            $query = Municipio::select('id', 'nome', 'codigo_ibge')
                ->orderBy('nome');

            // Todos os municípios são do Paraná, não é necessário filtrar por estado
            Log::info('Carregando todos os municípios (todos são do Paraná)');

            Log::info('Executando query SQL', ['sql' => $query->toSql()]);
            $municipios = $query->get();
            
            Log::info('Municípios encontrados', [
                'total' => $municipios->count(),
                'primeiros_3' => $municipios->take(3)->pluck('nome')
            ]);

            return response()->json([
                'data' => $municipios
            ]);
        } catch (\Exception $e) {
            Log::error('=== ERRO EM BUSCAR MUNICÍPIOS ===', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao buscar municípios',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna estados para seleção (simplificado - apenas Paraná)
     */
    public function buscarEstados()
    {
        try {
            $estados = [
                ['sigla' => 'PR', 'nome' => 'Paraná']
            ];

            return response()->json([
                'data' => $estados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar estados',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica se o usuário tem as permissões necessárias
     */
    private function checkAccess($permissions, $requireAll = false)
    {
        /** @var \App\Models\Administracao\User $user */
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // 2. Verificação flexível de permissões
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }
        
        if ($requireAll) {
            // Todas as permissões são obrigatórias (AND)
            foreach ($permissions as $permission) {
                if (!$user->hasPermission($permission)) {
                    abort(403, "Permissão obrigatória: {$permission}");
                }
            }
        } else {
            // Pelo menos uma permissão é suficiente (OR)
            $hasAnyPermission = false;
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasAnyPermission = true;
                    break;
                }
            }
            
            if (!$hasAnyPermission) {
                abort(403, 'Acesso negado. Permissão insuficiente.');
            }
        }
        
        return true;
    }
}
