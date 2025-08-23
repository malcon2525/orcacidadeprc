<?php

namespace App\Http\Controllers\Api\Administracao\EstruturaOrcamento;

use App\Http\Controllers\Controller;
use App\Models\Administracao\EstruturaOrcamento\GrandeItem;
use App\Models\Administracao\EstruturaOrcamento\SubGrupo;
use App\Models\Administracao\EstruturaOrcamento\TipoOrcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\Logging\EstruturaOrcamentoLogService;

class ImportacaoController extends Controller
{
    /**
     * Service de log para Estrutura Orçamento
     */
    protected EstruturaOrcamentoLogService $logger;
    
    /**
     * Construtor com dependência do logger
     */
    public function __construct(EstruturaOrcamentoLogService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Importa estrutura de orçamento a partir de arquivo Excel
     */
    public function importar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eo_tipo_orcamento_id' => 'required|exists:eo_tipos_orcamentos,id',
            'arquivo' => 'required|file|mimes:xlsx,xls|max:10240' // 10MB
        ], [
            'eo_tipo_orcamento_id.required' => 'O tipo de orçamento é obrigatório',
            'eo_tipo_orcamento_id.exists' => 'O tipo de orçamento selecionado não existe',
            'arquivo.required' => 'O arquivo Excel é obrigatório',
            'arquivo.file' => 'O arquivo enviado não é válido',
            'arquivo.mimes' => 'O arquivo deve ser do tipo Excel (.xlsx ou .xls)',
            'arquivo.max' => 'O arquivo não pode ter mais de 10MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $tipoOrcamentoId = $request->eo_tipo_orcamento_id;
            $arquivo = $request->file('arquivo');

            $this->logger->inicioImportacao($tipoOrcamentoId, [
                'arquivo' => $arquivo->getClientOriginalName(),
                'tamanho' => $arquivo->getSize()
            ]);

            // Verificar se o tipo de orçamento existe
            $tipoOrcamento = TipoOrcamento::findOrFail($tipoOrcamentoId);

            // Processar arquivo Excel
            $this->logger->processamentoArquivo([
                'nome_arquivo' => $arquivo->getClientOriginalName(),
                'tamanho' => $arquivo->getSize()
            ]);
            $dados = $this->processarArquivoExcel($arquivo);

            // Validar estrutura dos dados
            $this->validarEstruturaDados($dados);

            // Limpar estrutura existente
            $this->logger->limpezaAnterior($tipoOrcamentoId);
            $this->limparEstruturaExistente($tipoOrcamentoId);

            // Criar nova estrutura
            $resultado = $this->criarNovaEstrutura($dados, $tipoOrcamentoId);

            DB::commit();

            $this->logger->sucessoImportacao([
                'tipo_orcamento_id' => $tipoOrcamentoId,
                'grandes_itens' => $resultado['grandes_itens'],
                'subgrupos' => $resultado['subgrupos']
            ]);

            // Preparar mensagem de resposta
            $mensagem = "Estrutura importada com sucesso!";
            if (!empty($resultado['erros'])) {
                $mensagem .= " (Alguns itens não puderam ser criados: " . count($resultado['erros']) . " erros)";
            }

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'data' => [
                    'tipo_orcamento' => $tipoOrcamento->descricao,
                    'grandes_itens_criados' => $resultado['grandes_itens'],
                    'subgrupos_criados' => $resultado['subgrupos'],
                    'total_erros' => count($resultado['erros']),
                    'erros' => $resultado['erros']
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->logger->erroImportacao($e->getMessage(), [
                'tipo_orcamento_id' => $request->eo_tipo_orcamento_id ?? 'não informado',
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar estrutura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processa arquivo Excel e extrai dados
     */
    private function processarArquivoExcel($arquivo)
    {
        try {
            $spreadsheet = IOFactory::load($arquivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Log do processamento inicial
            Log::info('Iniciando processamento do arquivo Excel', [
                'total_linhas' => count($rows),
                'nome_arquivo' => $arquivo->getClientOriginalName()
            ]);

            // Remover cabeçalho
            $headers = array_shift($rows);
            
            // Validar cabeçalhos
            $this->validarCabecalhos($headers);

            $estrutura = [];
            $linhasProcessadas = 0;
            $linhasIgnoradas = 0;

            foreach ($rows as $index => $row) {
                $linhaNumero = $index + 2; // +2 porque removemos o cabeçalho e começamos do 0
                
                // Pular linhas vazias ou com dados insuficientes
                if (empty(array_filter($row)) || count($row) < 2) {
                    $linhasIgnoradas++;
                    continue;
                }

                $codigo = trim($row[0]);
                $descricao = trim($row[1]);

                // Validar se código e descrição não estão vazios
                if (empty($codigo) || empty($descricao)) {
                    Log::warning('Linha ignorada - dados incompletos', [
                        'linha' => $linhaNumero,
                        'codigo' => $codigo,
                        'descricao' => $descricao
                    ]);
                    $linhasIgnoradas++;
                    continue;
                }

                try {
                    // Validar formato do código
                    $this->validarCodigo($codigo);

                    $partes = explode('.', $codigo);
                    $nivel1 = (int) $partes[0];
                    $nivel2 = (int) $partes[1];

                    if ($nivel2 === 0) {
                        // É um Grande Item (XX.00)
                        $estrutura[$nivel1] = [
                            'ordem' => $nivel1,
                            'descricao' => $descricao,
                            'subgrupos' => []
                        ];
                        
                        Log::info('Grande Item encontrado', [
                            'linha' => $linhaNumero,
                            'codigo' => $codigo,
                            'descricao' => $descricao
                        ]);
                    } else {
                        // É um Subgrupo (XX.YY onde YY > 0)
                        if (!isset($estrutura[$nivel1])) {
                            throw new \Exception("Subgrupo {$codigo} não tem um Grande Item correspondente ({$nivel1}.00) - Linha {$linhaNumero}");
                        }

                        $estrutura[$nivel1]['subgrupos'][] = [
                            'ordem' => $nivel2,
                            'descricao' => $descricao
                        ];
                        
                        Log::info('Subgrupo encontrado', [
                            'linha' => $linhaNumero,
                            'codigo' => $codigo,
                            'descricao' => $descricao,
                            'grande_item' => $nivel1
                        ]);
                    }

                    $linhasProcessadas++;

                } catch (\Exception $e) {
                    Log::error('Erro ao processar linha', [
                        'linha' => $linhaNumero,
                        'codigo' => $codigo,
                        'descricao' => $descricao,
                        'erro' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            // Log do resumo do processamento
            Log::info('Processamento do arquivo concluído', [
                'linhas_processadas' => $linhasProcessadas,
                'linhas_ignoradas' => $linhasIgnoradas,
                'grandes_itens_encontrados' => count($estrutura),
                'total_subgrupos' => array_sum(array_map(function($item) {
                    return count($item['subgrupos']);
                }, $estrutura))
            ]);

            return array_values($estrutura);

        } catch (\Exception $e) {
            Log::error('Erro ao processar arquivo Excel', [
                'erro' => $e->getMessage(),
                'arquivo' => $arquivo->getClientOriginalName()
            ]);
            throw new \Exception('Erro ao processar arquivo Excel: ' . $e->getMessage());
        }
    }

    /**
     * Valida cabeçalhos do Excel
     */
    private function validarCabecalhos($headers)
    {
        // Normalizar cabeçalhos (remover espaços e converter para minúsculo)
        $headersNormalizados = array_map(function($header) {
            return strtolower(trim($header));
        }, $headers);

        $cabecalhosEsperados = [
            'codigo',
            'descricao'
        ];

        $cabecalhosEncontrados = [];
        foreach ($cabecalhosEsperados as $cabecalho) {
            if (!in_array($cabecalho, $headersNormalizados)) {
                throw new \Exception("Cabeçalho obrigatório não encontrado: {$cabecalho}");
            }
            $cabecalhosEncontrados[] = $cabecalho;
        }

        // Log dos cabeçalhos encontrados para debug
        Log::info('Cabeçalhos validados com sucesso', [
            'cabecalhos_originais' => $headers,
            'cabecalhos_normalizados' => $headersNormalizados,
            'cabecalhos_encontrados' => $cabecalhosEncontrados
        ]);
    }

    /**
     * Valida formato do código hierárquico
     */
    private function validarCodigo($codigo)
    {
        // Padrão: XX.YY (apenas 2 níveis)
        if (!preg_match('/^\d{2}\.\d{2}$/', $codigo)) {
            throw new \Exception("Código inválido: '{$codigo}'. Formato esperado: XX.YY (ex: 01.00, 01.01, 02.00, 02.01)");
        }
        
        $partes = explode('.', $codigo);
        $nivel1 = (int) $partes[0];
        $nivel2 = (int) $partes[1];
        
        // Validar se o nível 1 não é zero
        if ($nivel1 === 0) {
            throw new \Exception("Código inválido: '{$codigo}'. O primeiro nível não pode ser zero (00.YY)");
        }
        
        // Validar se o nível 2 não excede 99 (limite prático)
        if ($nivel2 > 99) {
            throw new \Exception("Código de subgrupo muito alto: '{$codigo}'. Máximo permitido: XX.99");
        }
        
        // Log de validação bem-sucedida
        Log::debug('Código validado com sucesso', [
            'codigo' => $codigo,
            'nivel1' => $nivel1,
            'nivel2' => $nivel2,
            'tipo' => $nivel2 === 0 ? 'Grande Item' : 'Subgrupo'
        ]);
    }

    /**
     * Valida estrutura dos dados extraídos
     */
    private function validarEstruturaDados($dados)
    {
        if (empty($dados)) {
            throw new \Exception('Nenhum dado encontrado no arquivo. Verifique se o arquivo contém dados válidos.');
        }

        $totalGrandesItens = count($dados);
        $totalSubgrupos = 0;
        $erros = [];

        foreach ($dados as $index => $grandeItem) {
            $numeroGrandeItem = $index + 1;
            
            if (empty($grandeItem['descricao'])) {
                $erros[] = "Grande item #{$numeroGrandeItem} (código {$grandeItem['ordem']}.00): descrição não pode estar vazia";
            }

            if (empty($grandeItem['subgrupos'])) {
                $erros[] = "Grande item '{$grandeItem['descricao']}' (código {$grandeItem['ordem']}.00): deve ter pelo menos um subgrupo";
            } else {
                $totalSubgrupos += count($grandeItem['subgrupos']);
                
                foreach ($grandeItem['subgrupos'] as $subIndex => $subgrupo) {
                    $numeroSubgrupo = $subIndex + 1;
                    
                    if (empty($subgrupo['descricao'])) {
                        $erros[] = "Subgrupo #{$numeroSubgrupo} do grande item '{$grandeItem['descricao']}' (código {$grandeItem['ordem']}.{$subgrupo['ordem']}): descrição não pode estar vazia";
                    }
                }
            }
        }

        if (!empty($erros)) {
            $mensagemErro = "Erros de validação encontrados:\n" . implode("\n", $erros);
            throw new \Exception($mensagemErro);
        }

        // Log de validação bem-sucedida
        Log::info('Estrutura de dados validada com sucesso', [
            'total_grandes_itens' => $totalGrandesItens,
            'total_subgrupos' => $totalSubgrupos,
            'estrutura_valida' => true
        ]);
    }

    /**
     * Remove estrutura existente do tipo de orçamento
     */
    private function limparEstruturaExistente($tipoOrcamentoId)
    {
        // Excluir subgrupos primeiro (devido à chave estrangeira)
        SubGrupo::whereHas('grandeItem', function($query) use ($tipoOrcamentoId) {
            $query->where('eo_tipo_orcamento_id', $tipoOrcamentoId);
        })->delete();

        // Excluir grandes itens
        GrandeItem::where('eo_tipo_orcamento_id', $tipoOrcamentoId)->delete();
    }

    /**
     * Cria nova estrutura baseada nos dados do Excel
     */
    private function criarNovaEstrutura($dados, $tipoOrcamentoId)
    {
        $grandesItensCriados = 0;
        $subgruposCriados = 0;
        $erros = [];

        try {
            foreach ($dados as $grandeItemData) {
                try {
                    // Criar grande item
                    $grandeItem = GrandeItem::create([
                        'eo_tipo_orcamento_id' => $tipoOrcamentoId,
                        'descricao' => $grandeItemData['descricao'],
                        'ordem' => $grandeItemData['ordem']
                    ]);

                    $grandesItensCriados++;
                    
                    Log::info('Grande item criado com sucesso', [
                        'id' => $grandeItem->id,
                        'descricao' => $grandeItem->descricao,
                        'ordem' => $grandeItem->ordem,
                        'tipo_orcamento_id' => $tipoOrcamentoId
                    ]);

                    // Criar subgrupos
                    foreach ($grandeItemData['subgrupos'] as $subgrupoData) {
                        try {
                            $subgrupo = SubGrupo::create([
                                'eo_grande_item_id' => $grandeItem->id,
                                'descricao' => $subgrupoData['descricao'],
                                'ordem' => $subgrupoData['ordem']
                            ]);

                            $subgruposCriados++;
                            
                            Log::info('Subgrupo criado com sucesso', [
                                'id' => $subgrupo->id,
                                'descricao' => $subgrupo->descricao,
                                'ordem' => $subgrupo->ordem,
                                'grande_item_id' => $grandeItem->id
                            ]);

                        } catch (\Exception $e) {
                            $erroMsg = "Erro ao criar subgrupo '{$subgrupoData['descricao']}' (ordem {$subgrupoData['ordem']}): " . $e->getMessage();
                            $erros[] = $erroMsg;
                            Log::error($erroMsg, [
                                'subgrupo_data' => $subgrupoData,
                                'grande_item_id' => $grandeItem->id,
                                'erro' => $e->getMessage()
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    $erroMsg = "Erro ao criar grande item '{$grandeItemData['descricao']}' (ordem {$grandeItemData['ordem']}): " . $e->getMessage();
                    $erros[] = $erroMsg;
                    Log::error($erroMsg, [
                        'grande_item_data' => $grandeItemData,
                        'tipo_orcamento_id' => $tipoOrcamentoId,
                        'erro' => $e->getMessage()
                    ]);
                }
            }

            // Log do resumo da criação
            Log::info('Estrutura criada com sucesso', [
                'tipo_orcamento_id' => $tipoOrcamentoId,
                'grandes_itens_criados' => $grandesItensCriados,
                'subgrupos_criados' => $subgruposCriados,
                'total_erros' => count($erros)
            ]);

            // Se houve erros, logar todos eles
            if (!empty($erros)) {
                Log::warning('Alguns itens não puderam ser criados', [
                    'erros' => $erros,
                    'total_erros' => count($erros)
                ]);
            }

            return [
                'grandes_itens' => $grandesItensCriados,
                'subgrupos' => $subgruposCriados,
                'erros' => $erros
            ];

        } catch (\Exception $e) {
            Log::error('Erro crítico ao criar estrutura', [
                'tipo_orcamento_id' => $tipoOrcamentoId,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
