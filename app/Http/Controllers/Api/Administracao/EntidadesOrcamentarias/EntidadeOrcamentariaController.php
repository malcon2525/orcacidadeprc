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
            if ($request->has('jurisdicao_razao_social') && !empty($request->jurisdicao_razao_social)) {
                Log::info('Aplicando filtro jurisdicao_razao_social', ['valor' => $request->jurisdicao_razao_social]);
                $query->where('jurisdicao_razao_social', 'like', '%' . $request->jurisdicao_razao_social . '%');
            }

            if ($request->has('jurisdicao_nome_fantasia') && !empty($request->jurisdicao_nome_fantasia)) {
                Log::info('Aplicando filtro jurisdicao_nome_fantasia', ['valor' => $request->jurisdicao_nome_fantasia]);
                $query->where('jurisdicao_nome_fantasia', 'like', '%' . $request->jurisdicao_nome_fantasia . '%');
            }

            if ($request->has('tipo_organizacao') && !empty($request->tipo_organizacao)) {
                Log::info('Aplicando filtro tipo_organizacao', ['valor' => $request->tipo_organizacao]);
                $query->where('tipo_organizacao', $request->tipo_organizacao);
            }

            if ($request->has('nivel_administrativo') && !empty($request->nivel_administrativo)) {
                Log::info('Aplicando filtro nivel_administrativo', ['valor' => $request->nivel_administrativo]);
                $query->where('nivel_administrativo', $request->nivel_administrativo);
            }

            if ($request->has('jurisdicao_uf') && !empty($request->jurisdicao_uf)) {
                Log::info('Aplicando filtro jurisdicao_uf', ['valor' => $request->jurisdicao_uf]);
                $query->where('jurisdicao_uf', $request->jurisdicao_uf);
            }

            // Ordenação padrão
            $query->orderBy('jurisdicao_razao_social', 'asc');

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
                'filtros' => $request->only(['jurisdicao_razao_social', 'jurisdicao_nome_fantasia', 'tipo_organizacao'])
            ]);
            
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Retorna apenas id e jurisdicao_nome_fantasia como nome para campos de seleção
     */
    public function listarSelect()
    {
        try {
            $result = EntidadeOrcamentaria::select('id', 'jurisdicao_nome_fantasia as nome')
                ->where('ativo', true)
                ->orderBy('jurisdicao_nome_fantasia', 'asc')
                ->get();
                
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
                // CAMPOS OBRIGATÓRIOS
                'tipo_organizacao' => 'required|in:Unidade Federativa,Secretaria,Órgão,Autarquia,Consórcio,S/A,PJ,PF',
                'nivel_administrativo' => 'required|in:municipal,estadual,federal',
                'jurisdicao_razao_social' => 'required|string|max:255',
                'jurisdicao_nome_fantasia' => 'required|string|max:255',
                'jurisdicao_uf' => 'required|string|size:2',
                'email' => 'required|email|max:255',
                
                // CAMPOS OPCIONAIS
                'jurisdicao_codigo_ibge' => 'nullable|string|max:20',
                'cep' => 'nullable|string|max:20',
                'endereco' => 'nullable|string|max:255',
                'telefone' => 'nullable|string|max:20',
                'cnpj' => 'nullable|string|max:20',
                'observacao' => 'nullable|string',
                'responsavel' => 'nullable|string|max:255',
                'responsavel_cargo' => 'nullable|string|max:255',
                'ativo' => 'sometimes|boolean'
            ], [
                // Mensagens dos campos obrigatórios
                'tipo_organizacao.required' => 'O tipo de organização é obrigatório',
                'tipo_organizacao.in' => 'O tipo de organização selecionado é inválido',
                'nivel_administrativo.required' => 'O nível administrativo é obrigatório',
                'nivel_administrativo.in' => 'O nível administrativo selecionado é inválido',
                'jurisdicao_razao_social.required' => 'A razão social é obrigatória',
                'jurisdicao_nome_fantasia.required' => 'O nome fantasia é obrigatório',
                'jurisdicao_uf.required' => 'A UF é obrigatória',
                'jurisdicao_uf.size' => 'A UF deve ter exatamente 2 caracteres',
                'email.required' => 'O email é obrigatório',
                'email.email' => 'O email informado é inválido',
                
                // Mensagens dos campos opcionais
                'cnpj.max' => 'O CNPJ deve ter no máximo 20 caracteres',
                'telefone.max' => 'O telefone deve ter no máximo 20 caracteres',
                'cep.max' => 'O CEP deve ter no máximo 20 caracteres'
            ]);

            if ($validator->fails()) {
                Log::warning('Validação falhou no store', ['errors' => $validator->errors()]);
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
                // CAMPOS OBRIGATÓRIOS
                'tipo_organizacao' => 'required|in:Unidade Federativa,Secretaria,Órgão,Autarquia,Consórcio,S/A,PJ,PF',
                'nivel_administrativo' => 'required|in:municipal,estadual,federal',
                'jurisdicao_razao_social' => 'required|string|max:255',
                'jurisdicao_nome_fantasia' => 'required|string|max:255',
                'jurisdicao_uf' => 'required|string|size:2',
                'email' => 'required|email|max:255',
                
                // CAMPOS OPCIONAIS
                'jurisdicao_codigo_ibge' => 'nullable|string|max:20',
                'cep' => 'nullable|string|max:20',
                'endereco' => 'nullable|string|max:255',
                'telefone' => 'nullable|string|max:20',
                'cnpj' => 'nullable|string|max:20',
                'observacao' => 'nullable|string',
                'responsavel' => 'nullable|string|max:255',
                'responsavel_cargo' => 'nullable|string|max:255',
                'ativo' => 'sometimes|boolean'
            ], [
                // Mensagens dos campos obrigatórios
                'tipo_organizacao.required' => 'O tipo de organização é obrigatório',
                'tipo_organizacao.in' => 'O tipo de organização selecionado é inválido',
                'nivel_administrativo.required' => 'O nível administrativo é obrigatório',
                'nivel_administrativo.in' => 'O nível administrativo selecionado é inválido',
                'jurisdicao_razao_social.required' => 'A razão social é obrigatória',
                'jurisdicao_nome_fantasia.required' => 'O nome fantasia é obrigatório',
                'jurisdicao_uf.required' => 'A UF é obrigatória',
                'jurisdicao_uf.size' => 'A UF deve ter exatamente 2 caracteres',
                'email.required' => 'O email é obrigatório',
                'email.email' => 'O email informado é inválido'
            ]);

            if ($validator->fails()) {
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
            $this->checkAccess(['gerenciar_entidades_orcamentarias']);

            DB::beginTransaction();

            // Buscar todos os municípios
            $municipios = Municipio::all();
            
            if ($municipios->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum município encontrado para importar.',
                    'novos' => 0,
                    'atualizados' => 0
                ], 400);
            }

            $importados = 0;
            $atualizados = 0;

            foreach ($municipios as $municipio) {
                // Verificar se já existe uma entidade com mesmo código IBGE
                $entidadeExistente = EntidadeOrcamentaria::where('jurisdicao_codigo_ibge', $municipio->codigo_ibge)
                    ->where('nivel_administrativo', 'municipal')
                    ->first();

                $dadosEntidade = [
                    'tipo_organizacao' => 'Unidade Federativa',
                    'nivel_administrativo' => 'municipal',
                    'jurisdicao_razao_social' => $municipio->nome,
                    'jurisdicao_nome_fantasia' => "Prefeitura Municipal de {$municipio->nome}",
                    'jurisdicao_uf' => 'PR',
                    'jurisdicao_codigo_ibge' => $municipio->codigo_ibge,
                    'email' => $municipio->email ?? '',
                    'endereco' => $municipio->endereco_prefeitura ?? '',
                    'telefone' => $municipio->telefone ?? '',
                    'cnpj' => $municipio->cnpj ?? '',
                    'cep' => $municipio->cep ?? '',
                    'responsavel' => $municipio->prefeito ?? null,
                    'responsavel_cargo' => $municipio->prefeito ? 'Prefeito' : null,
                    'observacao' => 'Importado automaticamente da base de municípios do Paraná',
                    'ativo' => true
                ];

                if ($entidadeExistente) {
                    // Atualizar entidade existente
                    $entidadeExistente->update($dadosEntidade);
                    $atualizados++;
                } else {
                    // Criar nova entidade
                    EntidadeOrcamentaria::create($dadosEntidade);
                    $importados++;
                }
            }

            DB::commit();

            $this->logger->sucessoImportacao($importados, $atualizados, [
                'importado_por' => Auth::id(),
                'tipo_importacao' => 'municipios_parana'
            ]);

            return response()->json([
                'message' => 'Importação concluída com sucesso!',
                'novos' => $importados,
                'atualizados' => $atualizados,
                'total_processados' => $importados + $atualizados
            ]);

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            $this->logger->erroCritico('IMPORTACAO_MUNICIPIOS', $e->getMessage(), [
                'importado_por' => Auth::id()
            ]);
            
            return response()->json([
                'message' => 'Erro ao importar municípios: ' . $e->getMessage()
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