<?php

namespace App\Http\Controllers\Api\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// Log facade removido - agora usando service de log
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\TabelaOficial\Derpr\{
    DerprComposicao,
    DerprMaoDeObra,
    DerprEquipamento,
    DerprMaterial,
    DerprServico,
    DerprItemIncidencia,
    DerprTransporte,
    DerprFormulaTransporte
};
use App\Services\Logging\ImportarDerprLogService;

/**
 * Controller para importação de tabelas DER-PR
 * 
 * Este controller gerencia a importação de arquivos PDF e Excel das tabelas
 * oficiais do DER-PR, incluindo serviços gerais, insumos e importação em lote.
 */
class ImportarDerprController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct(ImportarDerprLogService $logger)
    {
        $this->middleware('auth');
        $this->logger = $logger;
    }
    
    /**
     * Service de log para DER-PR
     */
    protected ImportarDerprLogService $logger;

    /**
     * Verifica se o usuário tem permissão para acessar este controller
     */
    private function verificarPermissao()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user->isSuperAdmin() && !$user->hasRole('gerenciar_importacao_derpr')) {
            abort(403, 'Acesso negado. Permissão insuficiente.');
        }
    }

    /**
     * Processa o arquivo PDF de serviços gerais
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarServicosGerais(Request $request)
    {
        $this->verificarPermissao();
        
        try {
            // ===================================================================
            // ETAPA 1: LOG DE INÍCIO E VALIDAÇÃO
            // ===================================================================
            
            $this->logger->inicioAba(1, 'SERVICOS_GERAIS');
            
            // Validar arquivo enviado
            $validacao = $this->validarArquivoPDF($request);
            if (!$validacao['sucesso']) {
                $this->logger->erroValidacaoDerpr('ABA_1_SERVICOS_GERAIS', $validacao['mensagem']);
                return response()->json(['message' => $validacao['mensagem']], 400);
            }
            
            // Verificar e apagar diretórios anteriores de processamento DERPR
            $tempDir = storage_path('app/private/temp');
            if (is_dir($tempDir)) {
                $subdirs = glob($tempDir . '/processado_derpr_*', GLOB_ONLYDIR);
                foreach ($subdirs as $subdir) {
                    $this->removeDirectory($subdir);
                }
            }
            
            $arquivo = $request->file('arquivo');
            
            // Salvar arquivo temporariamente
            $caminhoCompleto = $this->salvarArquivoTemporario($arquivo);
            
            // Criar diretório único para arquivos processados
            $nomeSaidaDir = 'processado_derpr_' . uniqid();
            $diretorioRelativoSaida = 'temp/' . $nomeSaidaDir;
            Storage::makeDirectory($diretorioRelativoSaida);
            $diretorioAbsolutoSaida = storage_path('app/' . $diretorioRelativoSaida);
            
            // Armazenar nome do diretório na session para uso nas outras abas
            session(['derpr_processamento_dir' => $nomeSaidaDir]);
            
            // Executar script Python
            $dados = $this->executarScriptPython($caminhoCompleto, 'servicos');
            
            // Limpar arquivo temporário
            Storage::delete($caminhoCompleto);
            
            $this->logger->sucessoAba(1, 'SERVICOS_GERAIS', [
                'total_composicoes' => count($dados)
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $dados,
                'message' => 'Arquivo processado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            $this->logger->erroCriticoDerpr('ABA_1_SERVICOS_GERAIS', $e->getMessage());
            
            return response()->json([
                'message' => 'Ocorreu um erro ao processar o arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processa o arquivo PDF de insumos
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarInsumos(Request $request)
    {
        $this->verificarPermissao();
        
        try {
            // ===================================================================
            // ETAPA 1: LOG DE INÍCIO E VALIDAÇÃO
            // ===================================================================
            
            $this->logger->inicioAba(2, 'INSUMOS');
            
            // Validar arquivo enviado
            $validacao = $this->validarArquivoPDF($request);
            if (!$validacao['sucesso']) {
                $this->logger->erroValidacaoDerpr('ABA_2_INSUMOS', $validacao['mensagem']);
                return response()->json(['message' => $validacao['mensagem']], 400);
            }
            
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('ABA_2_INSUMOS', 'Diretório de processamento não encontrado na sessão');
                return response()->json([
                    'message' => 'É necessário processar primeiro a Aba 1 (Serviços Gerais)'
                ], 400);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            if (!file_exists($diretorioProcessamento)) {
                $this->logErroValidacao('ABA_2_INSUMOS', 'Diretório de processamento não existe no sistema');
                return response()->json([
                    'message' => 'Diretório de processamento não encontrado. Processe primeiro a Aba 1.'
                ], 400);
            }
            
            $arquivo = $request->file('arquivo');
            $caminhoCompleto = $this->salvarArquivoTemporario($arquivo);
            
            // Executar script Python
            $dados = $this->executarScriptPython($caminhoCompleto, 'insumos');
            
            // Limpar arquivo temporário
            Storage::delete($caminhoCompleto);
            
            $this->logger->sucessoAba(2, 'INSUMOS', [
                'total_insumos' => array_sum([
                    count($dados['equipamentos'] ?? []),
                    count($dados['mao_de_obra'] ?? []),
                    count($dados['materiais'] ?? []),
                    count($dados['servicos'] ?? []),
                    count($dados['transportes'] ?? [])
                ])
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $dados,
                'message' => 'Arquivo processado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            $this->logger->erroCriticoDerpr('ABA_2_INSUMOS', $e->getMessage());
            
            return response()->json([
                'message' => 'Ocorreu um erro ao processar o arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processa o arquivo PDF de fórmulas de transporte
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarFormulasTransporte(Request $request)
    {
        $this->verificarPermissao();
        
        try {
            // ===================================================================
            // ETAPA 1: LOG DE INÍCIO E VALIDAÇÃO
            // ===================================================================
            
            $this->logger->inicioAba(3, 'FORMULAS_TRANSPORTE');
            
            // Validar arquivo enviado
            $validacao = $this->validarArquivoPDF($request);
            if (!$validacao['sucesso']) {
                $this->logger->erroValidacaoDerpr('ABA_3_FORMULAS_TRANSPORTE', $validacao['mensagem']);
                return response()->json(['message' => $validacao['mensagem']], 400);
            }
            
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('ABA_3_FORMULAS_TRANSPORTE', 'Diretório de processamento não encontrado na sessão');
                return response()->json([
                    'message' => 'É necessário processar primeiro a Aba 1 (Serviços Gerais)'
                ], 400);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            if (!file_exists($diretorioProcessamento)) {
                $this->logErroValidacao('ABA_3_FORMULAS_TRANSPORTE', 'Diretório de processamento não existe no sistema');
                return response()->json([
                    'message' => 'Diretório de processamento não encontrado. Processe primeiro a Aba 1.'
                ], 400);
            }
            
            $arquivo = $request->file('arquivo');
            $caminhoCompleto = $this->salvarArquivoTemporario($arquivo);
            
            // Executar script Python
            $dados = $this->executarScriptPython($caminhoCompleto, 'formulas_transporte');
            
            // Limpar arquivo temporário
            Storage::delete($caminhoCompleto);
            
            $this->logger->sucessoAba(3, 'FORMULAS_TRANSPORTE', [
                'total_formulas' => isset($dados['data']) ? count($dados['data']) : count($dados)
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $dados,
                'message' => 'Arquivo processado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            $this->logger->erroCriticoDerpr('ABA_3_FORMULAS_TRANSPORTE', $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar o arquivo: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Valida se o arquivo PDF foi enviado corretamente
     * 
     * @param Request $request
     * @return array
     */
    private function validarArquivoPDF(Request $request): array
    {
        if (!$request->hasFile('arquivo')) {
            return [
                'sucesso' => false,
                'mensagem' => 'Nenhum arquivo foi enviado.'
            ];
        }

        $arquivo = $request->file('arquivo');
        
        if ($arquivo->getClientOriginalExtension() !== 'pdf') {
            return [
                'sucesso' => false,
                'mensagem' => 'O arquivo deve ser um PDF.'
            ];
        }

        

        return ['sucesso' => true];
    }

    /**
     * Salva o arquivo temporariamente e retorna o caminho completo
     * 
     * @param \Illuminate\Http\UploadedFile $arquivo
     * @return string
     */
    private function salvarArquivoTemporario($arquivo): string
    {
        $caminhoTemp = $arquivo->storeAs('temp', uniqid() . '.pdf');
        return Storage::path($caminhoTemp); // Cross-platform path
    }





    /**
     * Executa o script Python correspondente ao tipo de processamento
     * 
     * @param string $caminhoArquivo
     * @param string $tipo
     * @return array
     * @throws \Exception
     */
    private function executarScriptPython(string $caminhoArquivo, string $tipo): array
    {
        $scripts = [
            'servicos' => '01.Importar-DER-PR-Tabela-Servicos.py',
            'insumos' => '02.Importar-DER-PR-Tabela-Insumos.py',
            'formulas_transporte' => '03.Importar-DER-PR-Formulas-Transporte.py'
        ];

        if (!isset($scripts[$tipo])) {
            throw new \Exception("Tipo de processamento inválido: {$tipo}");
        }

        $scriptPython = base_path("01_python/importacao_DERPR/{$scripts[$tipo]}");
        
        $comando = "python " . escapeshellarg($scriptPython) . " " . escapeshellarg($caminhoArquivo);
        
        $saida = shell_exec($comando);



        // Limpar a saída e decodificar o JSON
        $saida = trim($saida);
        $dados = json_decode($saida, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logErroValidacao('JSON_DECODE_ERROR', 'Erro ao decodificar JSON', [
                'erro' => json_last_error_msg(),
                'saida_python' => $saida,
                'tipo' => $tipo,
                'tamanho_saida' => strlen($saida)
            ]);
            throw new \Exception("Erro ao decodificar JSON do script Python: " . json_last_error_msg() . ". Saída: " . substr($saida, 0, 200));
        }

        // VERIFICAR SE HÁ ERRO na resposta do Python
        if (isset($dados['error'])) {
            $this->logErroValidacao('PYTHON_ERROR', 'Script Python retornou erro', [
                'erro' => $dados['error'],
                'tipo' => $tipo
            ]);
            throw new \Exception("Erro no script Python: " . $dados['error']);
        }
        
        // Salvar dados em Excel se for serviços gerais
        if ($tipo === 'servicos' && !empty($dados)) {
            $this->salvarDadosEmExcel($dados, 'derpr_composicoes.xlsx');
        }
        
        // Salvar dados em Excel se for insumos
        if ($tipo === 'insumos' && !empty($dados)) {
            $this->salvarDadosInsumosEmExcel($dados);
        }
        
        // Salvar dados em Excel se for fórmulas de transporte
        if ($tipo === 'formulas_transporte' && !empty($dados)) {
            // Verificar se os dados estão na estrutura correta
            if (isset($dados['data']) && is_array($dados['data'])) {
                $dadosParaSalvar = $dados['data'];
            } elseif (is_array($dados)) {
                $dadosParaSalvar = $dados;
            } else {
                $this->logErroValidacao('FORMULAS_TRANSPORTE', 'Estrutura de dados inválida', ['tipo_recebido' => gettype($dados)]);
                return $dados;
            }
            
            $this->salvarDadosFormulasTransporteEmExcel($dadosParaSalvar);
        }

        return $dados;
    }

    /**
     * Salva dados em arquivo Excel
     * 
     * @param array $dados
     * @param string $nomeArquivo
     * @return void
     */
    private function salvarDadosEmExcel(array $dados, string $nomeArquivo): void
    {
        try {
            // Obter diretório da sessão ou criar novo se não existir
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                throw new \Exception('Diretório de processamento não encontrado na sessão');
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            // Criar diretório se não existir
            if (!file_exists($diretorioProcessamento)) {
                mkdir($diretorioProcessamento, 0755, true);
            }

            // Criar planilha
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Definir cabeçalhos
            $headers = [
                'grupo', 'data_base', 'honerado', 'codigo', 'descricao', 
                'unidade', 'custo_execucao', 'custo_material', 'custo_sub_servico', 
                'custo_unitario', 'transporte'
            ];
            
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1', $header);
                $col++;
            }

            // Adicionar dados
            $row = 2;
            foreach ($dados as $item) {
                $col = 1;
                foreach ($headers as $header) {
                    $value = $item[$header] ?? '';
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, $value);
                    $col++;
                }
                $row++;
            }

            // Salvar arquivo
            $caminhoArquivo = $diretorioProcessamento . '/' . $nomeArquivo;
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($caminhoArquivo);

            // Gerar metadata
            $metadata = [
                'arquivo_original' => 'PDF processado',
                'data_processamento' => now()->toISOString(),
                'total_registros' => count($dados),
                'usuario' => auth()->user() ? auth()->user()->name : 'N/A',
                'ip' => request()->ip()
            ];

            $arquivoMetadata = $diretorioProcessamento . '/derpr_metadata.json';
            file_put_contents($arquivoMetadata, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Arquivo Excel salvo com sucesso

        } catch (\Exception $e) {
            $this->logErroCritico('SALVAR_EXCEL', $e->getMessage());
        }
    }

    /**
     * Salva dados de insumos em 6 arquivos Excel separados
     * 
     * @param array $dados
     * @return void
     */
    private function salvarDadosInsumosEmExcel(array $dados): void
    {
        try {
            // Obter diretório da sessão
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                throw new \Exception('Diretório de processamento não encontrado na sessão');
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            // Criar diretório se não existir
            if (!file_exists($diretorioProcessamento)) {
                mkdir($diretorioProcessamento, 0755, true);
            }

            // Definir os 6 tipos de arquivos
            $tiposArquivos = [
                'equipamentos' => 'derpr_equipamentos.xlsx',
                'mao_de_obra' => 'derpr_mao_de_obra.xlsx',
                'itens_incidencia' => 'derpr_itens_incidencia.xlsx',
                'materiais' => 'derpr_materiais.xlsx',
                'servicos' => 'derpr_servicos.xlsx',
                'transportes' => 'derpr_transportes.xlsx'
            ];

            $arquivosProcessados = [];
            
            // Salvar cada tipo de dados em um arquivo separado
            foreach ($tiposArquivos as $tipo => $nomeArquivo) {
                if (!empty($dados[$tipo])) {
                    $this->salvarTipoInsumoEmExcel($dados[$tipo], $diretorioProcessamento . '/' . $nomeArquivo, $tipo);
                    $arquivosProcessados[] = $nomeArquivo;
                }
            }

            // Gerar metadata
            $metadata = [
                'arquivo_original' => 'PDF processado',
                'data_processamento' => now()->toISOString(),
                'total_equipamentos' => count($dados['equipamentos'] ?? []),
                'total_mao_de_obra' => count($dados['mao_de_obra'] ?? []),
                'total_itens_incidencia' => count($dados['itens_incidencia'] ?? []),
                'total_materiais' => count($dados['materiais'] ?? []),
                'total_servicos' => count($dados['servicos'] ?? []),
                'total_transportes' => count($dados['transportes'] ?? []),
                'usuario' => auth()->user() ? auth()->user()->name : 'N/A',
                'ip' => request()->ip()
            ];

            $arquivoMetadata = $diretorioProcessamento . '/derpr_metadata_insumos.json';
            file_put_contents($arquivoMetadata, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Arquivos Excel de insumos salvos com sucesso

        } catch (\Exception $e) {
            $this->logErroCritico('SALVAR_EXCEL_INSUMOS', $e->getMessage());
        }
    }

    /**
     * Salva um tipo específico de insumo em Excel
     * 
     * @param array $dados
     * @param string $caminhoArquivo
     * @param string $tipo
     * @return void
     */
    private function salvarTipoInsumoEmExcel(array $dados, string $caminhoArquivo, string $tipo): void
    {
        // Criar planilha
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Definir cabeçalhos baseados no tipo
        $headers = $this->getHeadersPorTipo($tipo);
        
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1', $header);
            $col++;
        }

        // Adicionar dados
        $row = 2;
        foreach ($dados as $item) {
            $col = 1;
            foreach ($headers as $header) {
                $value = $item[$header] ?? '';
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, $value);
                $col++;
            }
            $row++;
        }

        // Salvar arquivo
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($caminhoArquivo);
    }

    /**
     * Retorna os cabeçalhos para cada tipo de insumo
     * 
     * @param string $tipo
     * @return array
     */
    private function getHeadersPorTipo(string $tipo): array
    {
        $headers = [
            'equipamentos' => [
                'codigo_servico', 'descricao_servico', 'unidade_servico', 'data_base', 'honerado',
                'descricao', 'codigo_equipamento', 'quantidade', 'ut_produtiva', 'ut_improdutiva',
                'vl_hr_prod', 'vl_hr_imp', 'custo_horario'
            ],
            'mao_de_obra' => [
                'codigo_servico', 'descricao_servico', 'unidade_servico', 'data_base', 'honerado',
                'descricao', 'codigo', 'eq_salarial', 'encargos_percentagem', 'sal_hora',
                'consumo', 'custo_horario'
            ],
            'itens_incidencia' => [
                'codigo_servico', 'descricao_servico', 'unidade_servico', 'data_base', 'honerado',
                'descricao', 'codigo', 'percentagem', 'tem_mo', 'custo'
            ],
            'materiais' => [
                'codigo_servico', 'descricao_servico', 'unidade_servico', 'data_base', 'honerado',
                'descricao', 'codigo', 'unid', 'custo_unitario', 'consumo', 'custo_unitario_final'
            ],
            'servicos' => [
                'codigo_servico', 'descricao_servico', 'unidade_servico', 'data_base', 'honerado',
                'descricao', 'codigo', 'unid', 'custo_unitario', 'consumo', 'custo_unitario_final'
            ],
            'transportes' => [
                'codigo_servico', 'descricao_servico', 'unidade_servico', 'data_base', 'honerado',
                'descricao', 'codigo', 'unid', 'formula1', 'formula2', 'custo', 'consumo', 'custo_unitario'
            ]
        ];

        return $headers[$tipo] ?? [];
    }

    /**
     * Processa arquivos automaticamente do diretório da sessão
     * 
     * @param string $diretorioProcessamento
     * @param array $configuracao
     * @return array
     */
    private function processarArquivosAutomaticos(string $diretorioProcessamento, array $configuracao): array
    {
        $resultados = [];
        
        // Listar todos os arquivos disponíveis no diretório
        $arquivosDisponiveis = [
            'derpr_composicoes.xlsx',
            'derpr_equipamentos.xlsx',
            'derpr_mao_de_obra.xlsx',
            'derpr_itens_incidencia.xlsx',
            'derpr_materiais.xlsx',
            'derpr_servicos.xlsx',
            'derpr_transportes.xlsx',
            'formulas_transporte.xlsx'
        ];
        
        foreach ($arquivosDisponiveis as $nomeArquivo) {
            $caminhoArquivo = $diretorioProcessamento . '/' . $nomeArquivo;
            
            if (file_exists($caminhoArquivo)) {
                $config = $configuracao[$nomeArquivo];
                $resultado = $this->processarESalvarArquivo($caminhoArquivo, $nomeArquivo, $config);
                $resultados[$nomeArquivo] = $resultado;
            }
        }
        
        return $resultados;
    }

    /**
     * Verifica arquivos disponíveis para gravação automática
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificarArquivosDisponiveis()
    {
        $this->verificarPermissao();
        
        try {
            $nomeDiretorio = session('derpr_processamento_dir');
            
            if (!$nomeDiretorio) {
                return response()->json([
                    'success' => true,
                    'status' => 'sem_diretorio',
                    'message' => 'Nenhum diretório de processamento encontrado',
                    'total_disponiveis' => 0,
                    'total_esperados' => 8,
                    'pode_gravar' => false,
                    'arquivos_disponiveis' => [],
                    'arquivos_faltantes' => [
                        'derpr_composicoes.xlsx',
                        'derpr_equipamentos.xlsx',
                        'derpr_mao_de_obra.xlsx',
                        'derpr_itens_incidencia.xlsx',
                        'derpr_materiais.xlsx',
                        'derpr_servicos.xlsx',
                        'derpr_transportes.xlsx',
                        'formulas_transporte.xlsx'
                    ],
                    'diretorio' => null
                ]);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            if (!file_exists($diretorioProcessamento)) {
                return response()->json([
                    'success' => true,
                    'status' => 'diretorio_inexistente',
                    'message' => 'Diretório de processamento não existe',
                    'total_disponiveis' => 0,
                    'total_esperados' => 8,
                    'pode_gravar' => false,
                    'arquivos_disponiveis' => [],
                    'arquivos_faltantes' => [
                        'derpr_composicoes.xlsx',
                        'derpr_equipamentos.xlsx',
                        'derpr_mao_de_obra.xlsx',
                        'derpr_itens_incidencia.xlsx',
                        'derpr_materiais.xlsx',
                        'derpr_servicos.xlsx',
                        'derpr_transportes.xlsx',
                        'formulas_transporte.xlsx'
                    ],
                    'diretorio' => $nomeDiretorio
                ]);
            }
            
            $arquivosEsperados = [
                'derpr_composicoes.xlsx',
                'derpr_equipamentos.xlsx',
                'derpr_mao_de_obra.xlsx',
                'derpr_itens_incidencia.xlsx',
                'derpr_materiais.xlsx',
                'derpr_servicos.xlsx',
                'derpr_transportes.xlsx',
                'formulas_transporte.xlsx'
            ];
            
            $arquivosDisponiveis = [];
            $arquivosFaltantes = [];
            
            foreach ($arquivosEsperados as $arquivo) {
                $caminhoArquivo = $diretorioProcessamento . '/' . $arquivo;
                if (file_exists($caminhoArquivo)) {
                    $arquivosDisponiveis[] = [
                        'nome' => $arquivo,
                        'tamanho' => filesize($caminhoArquivo),
                        'data_modificacao' => date('Y-m-d H:i:s', filemtime($caminhoArquivo))
                    ];
                } else {
                    $arquivosFaltantes[] = $arquivo;
                }
            }
            
            $totalDisponiveis = count($arquivosDisponiveis);
            $podeGravar = $totalDisponiveis === count($arquivosEsperados);
            $status = $podeGravar ? 'completo' : ($totalDisponiveis > 0 ? 'incompleto' : 'sem_arquivos');
            
            // Verificação de arquivos concluída
            
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $podeGravar ? 'Todos os arquivos estão disponíveis' : 'Alguns arquivos estão faltando',
                'total_disponiveis' => $totalDisponiveis,
                'total_esperados' => count($arquivosEsperados),
                'pode_gravar' => $podeGravar,
                'arquivos_disponiveis' => $arquivosDisponiveis,
                'arquivos_faltantes' => $arquivosFaltantes,
                'diretorio' => $nomeDiretorio
            ]);
            
        } catch (\Exception $e) {
            $this->logErroCritico('VERIFICAR_ARQUIVOS', $e->getMessage());
            
            return response()->json([
                'success' => false,
                'status' => 'erro',
                'message' => 'Erro ao verificar arquivos: ' . $e->getMessage(),
                'total_disponiveis' => 0,
                'total_esperados' => 8,
                'pode_gravar' => false,
                'arquivos_disponiveis' => [],
                'arquivos_faltantes' => [],
                'diretorio' => null
            ], 500);
        }
    }

    // Método de log removido - agora usando service de log

    /**
     * Remove um diretório e todo seu conteúdo recursivamente
     * 
     * @param string $dir Caminho do diretório a ser removido
     * @return bool
     */
    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }

    /**
     * Importa múltiplos arquivos Excel em lote
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importarLote(Request $request)
    {
        $this->verificarPermissao();
        
        $tempoInicio = microtime(true);
        
        try {
            $this->logger->gravacaoBanco();
            
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('GRAVACAO_BANCO_DERPR', 'Diretório de processamento não encontrado na sessão');
                return response()->json([
                    'success' => false,
                    'message' => 'É necessário processar primeiro as Abas 1, 2 e 3'
                ], 400);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            if (!file_exists($diretorioProcessamento)) {
                $this->logErroValidacao('GRAVACAO_BANCO_DERPR', 'Diretório de processamento não existe no sistema');
                return response()->json([
                    'success' => false,
                    'message' => 'Diretório de processamento não encontrado. Processe primeiro as Abas 1, 2, 3 e 4.'
                ], 400);
            }
            
            $arquivosEsperados = [
                'derpr_composicoes.xlsx',
                'derpr_equipamentos.xlsx',
                'derpr_mao_de_obra.xlsx',
                'derpr_itens_incidencia.xlsx',
                'derpr_materiais.xlsx',
                'derpr_servicos.xlsx',
                'derpr_transportes.xlsx',
                'formulas_transporte.xlsx'
            ];
            
            $arquivosFaltantes = [];
            foreach ($arquivosEsperados as $arquivo) {
                if (!file_exists($diretorioProcessamento . '/' . $arquivo)) {
                    $arquivosFaltantes[] = $arquivo;
                }
            }
            
            if (!empty($arquivosFaltantes)) {
                $this->logErroValidacao('GRAVACAO_BANCO_DERPR', 'Arquivos faltando para importação', ['arquivos_faltantes' => $arquivosFaltantes]);
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivos faltando: ' . implode(', ', $arquivosFaltantes) . '. Processe primeiro as Abas 1, 2 e 3.'
                ], 400);
            }
            
            $configuracao = $this->getConfiguracaoArquivos();
            
            // Início da gravação automática no banco
            
            try {
                // Processar e salvar dados
                $resultados = $this->processarArquivosAutomaticos($diretorioProcessamento, $configuracao);
                
                // Calcular tempo de processamento
                $tempoProcessamento = microtime(true) - $tempoInicio;
                
                // Gravação concluída com sucesso
                
                // Gerar mensagem de resumo
                $mensagem = $this->gerarMensagemResumo($resultados);
                
                // Atualizar view de composições
                $this->atualizarViewComposicoes();
                
                $this->logger->sucesso('GRAVACAO_BANCO_DERPR', [
                    'tempo_processamento' => round($tempoProcessamento, 2) . 's',
                    'total_arquivos' => count($resultados)
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $mensagem,
                    'resultados' => $resultados
                ], 200);
                
            } catch (\Exception $e) {
                $tempoProcessamento = microtime(true) - $tempoInicio;
                
                // Log do erro na gravação
                
                throw $e;
            }
            
        } catch (\Exception $e) {
            $this->logger->erroCriticoDerpr('GRAVACAO_BANCO_DERPR', $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar arquivos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valida se os arquivos foram enviados para importação em lote
     * 
     * @param Request $request
     * @return array
     */
    private function validarArquivosLote(Request $request): array
    {
        if (!$request->hasFile('arquivos')) {
            $this->logErroValidacao('VALIDACAO_LOTE', 'Nenhum arquivo enviado para importação em lote');
            return [
                'sucesso' => false,
                'mensagem' => 'Nenhum arquivo enviado.'
            ];
        }
        
        return ['sucesso' => true];
    }

    // Método de log removido - verbosidade desnecessária

    /**
     * Salva os arquivos temporariamente
     * 
     * @param array $arquivos
     * @return array
     */
    private function salvarArquivosTemporarios($arquivos): array
    {
        $arquivosTemp = [];
        foreach ($arquivos as $arquivo) {
            $nomeArquivo = $arquivo->getClientOriginalName();
            $caminhoTemp = $arquivo->storeAs('temp/derpr', $nomeArquivo);
            $arquivosTemp[$nomeArquivo] = storage_path('app/' . $caminhoTemp);
        }
        return $arquivosTemp;
    }

    /**
     * Valida as colunas de todos os arquivos
     * 
     * @param array $arquivos
     * @param array $configuracao
     * @return array
     */
    private function validarColunasArquivos($arquivos, $configuracao): array
    {
        $validacoes = [];
        foreach ($configuracao as $nomeEsperado => $config) {
            $arquivo = collect($arquivos)->first(function($f) use ($nomeEsperado) {
                return strtolower($f->getClientOriginalName()) === strtolower($nomeEsperado);
            });

            $validacao = [
                'arquivo' => $nomeEsperado,
                'nome' => $arquivo ? 'ok' : 'não encontrado',
                'colunas' => 'não validado'
            ];

            if ($arquivo) {
                try {
                    $this->validarColunasArquivo($arquivo, $nomeEsperado, $config['colunas_obrigatorias']);
                    $validacao['colunas'] = 'ok';
                } catch (\Exception $e) {
                    $validacao['colunas'] = $e->getMessage();
                }
            }

            $validacoes[] = $validacao;
        }
        
        return $validacoes;
    }

    /**
     * Verifica se todos os arquivos foram validados com sucesso
     * 
     * @param array $validacoes
     * @return bool
     */
    private function todosArquivosValidos($validacoes): bool
    {
        return collect($validacoes)->every(function($v) {
            return $v['nome'] === 'ok' && $v['colunas'] === 'ok';
        });
    }

    /**
     * Processa todos os arquivos do lote
     * 
     * @param array $arquivosTemp
     * @param array $configuracao
     * @return array
     */
    private function processarArquivosLote($arquivosTemp, $configuracao): array
    {
        $resultados = [];
        foreach ($configuracao as $nomeEsperado => $config) {
            if (isset($arquivosTemp[$nomeEsperado])) {
                // Log do início do processamento do arquivo
                $this->logarProcessamentoArquivo($nomeEsperado, 'INICIADO', [
                    'tipo_arquivo' => $nomeEsperado,
                    'model' => $config['model']
                ]);
                
                $arquivo = new \Illuminate\Http\UploadedFile(
                    $arquivosTemp[$nomeEsperado],
                    $nomeEsperado,
                    mime_content_type($arquivosTemp[$nomeEsperado]),
                    null,
                    true
                );

                try {
                    $resultado = $this->processarESalvarArquivo($arquivo, $nomeEsperado, $config);
                    $resultados[$nomeEsperado] = $resultado;
                    
                    // Log do sucesso do processamento
                    $this->logarProcessamentoArquivo($nomeEsperado, 'CONCLUIDO', [
                        'registros_inseridos' => $resultado['registros_inseridos'],
                        'registros_atualizados' => $resultado['registros_atualizados'],
                        'total_registros' => $resultado['total_registros']
                    ]);
                    
                } catch (\Exception $e) {
                    // Log do erro no processamento
                    $this->logarErroGravacao($nomeEsperado, $e->getMessage(), [
                        'tipo_arquivo' => $nomeEsperado,
                        'model' => $config['model']
                    ]);
                    throw $e;
                }
            }
        }
        return $resultados;
    }

    /**
     * Gera mensagem de resumo do processamento
     * 
     * @param array $resultados
     * @return string
     */
    private function gerarMensagemResumo($resultados): string
    {
        $mensagem = "Arquivos processados com sucesso:";
        foreach ($resultados as $arquivo => $resultado) {
            $mensagem .= "\n" . sprintf(
                "%s: %d registros processados (%d inseridos, %d atualizados)",
                $arquivo,
                $resultado['total_registros'],
                $resultado['registros_inseridos'],
                $resultado['registros_atualizados']
            );
        }
        return $mensagem;
    }

    // Método de log removido - agora usando service de log

    /**
     * Atualiza a view de composições
     */
    private function atualizarViewComposicoes(): void
    {
        // Identificar todas as combinações de data_base e desoneracao presentes nas composições importadas
        $combinacoes = DB::table('derpr_composicoes')
            ->select('data_base', 'desoneracao')
            ->distinct()
            ->get();
            
        foreach ($combinacoes as $comb) {
            DB::table('derpr_composicoes_view')
                ->where('data_base', $comb->data_base)
                ->where('desoneracao', $comb->desoneracao)
                ->delete();
        }
        
        $composicoes = DB::table('derpr_composicoes')->get();
        foreach ($composicoes as $comp) {
            DB::table('derpr_composicoes_view')->insert([
                'data_base'        => $comp->data_base,
                'desoneracao'      => $comp->desoneracao,
                'codigo'           => $comp->codigo,
                'descricao'        => $comp->descricao,
                'unidade'          => $comp->unidade,
                'transporte'       => $comp->transporte,
                'valor_mao_obra'   => ($comp->custo_execucao ?? 0) + ($comp->custo_sub_servico ?? 0),
                'valor_mat_equip'  => ($comp->custo_unitario ?? 0) - (($comp->custo_execucao ?? 0) + ($comp->custo_sub_servico ?? 0)),
                'valor_total'      => $comp->custo_unitario ?? 0,
            ]);
        }
    }

    /**
     * Limpa os arquivos temporários
     * 
     * @param array $arquivosTemp
     */
    private function limparArquivosTemporarios($arquivosTemp): void
    {
        foreach ($arquivosTemp as $caminho) {
            if (file_exists($caminho)) {
                unlink($caminho);
            }
        }
        
        // Remover diretório temporário se estiver vazio
        $dirTemp = Storage::path('temp/derpr');
        if (is_dir($dirTemp) && count(scandir($dirTemp)) <= 2) {
            rmdir($dirTemp);
        }
    }

    // Métodos de log removidos - agora usando service de log

    /**
     * Configuração dos arquivos esperados e suas colunas obrigatórias
     */
    private function getConfiguracaoArquivos()
    {
        return [
            'derpr_composicoes.xlsx' => [
                'colunas_obrigatorias' => [
                    'grupo', 'data_base', 'honerado', 'codigo', 'descricao', 
                    'unidade', 'custo_execucao', 'custo_material', 
                    'custo_sub_servico', 'custo_unitario', 'transporte'
                ],
                'model' => DerprComposicao::class
            ],
            'derpr_equipamentos.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo_equipamento', 
                    'quantidade', 'ut_produtiva', 'ut_improdutiva', 
                    'vl_hr_prod', 'vl_hr_imp', 'custo_horario'
                ],
                'model' => DerprEquipamento::class
            ],
            'derpr_mao_de_obra.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 
                    'eq_salarial', 'encargos_percentagem', 'sal_hora', 
                    'consumo', 'custo_horario'
                ],
                'model' => DerprMaoDeObra::class
            ],
            'derpr_itens_incidencia.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 
                    'percentagem', 'tem_mo', 'custo'
                ],
                'model' => DerprItemIncidencia::class
            ],
            'derpr_materiais.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 'unid', 
                    'custo_unitario', 'consumo', 'custo_unitario_final'
                ],
                'model' => DerprMaterial::class
            ],
            'derpr_servicos.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 'unid', 
                    'custo_unitario', 'consumo', 'custo_unitario_final'
                ],
                'model' => DerprServico::class
            ],
            'derpr_transportes.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 'unidade', 
                    'formula1', 'formula2', 'consumo'
                ],
                'model' => DerprTransporte::class
            ],
            'formulas_transporte.xlsx' => [
                'colunas_obrigatorias' => [
                    'data_base', 'desoneracao', 'codigo', 'descricao', 
                    'unidade', 'formula_transporte'
                ],
                'model' => DerprFormulaTransporte::class
            ]
        ];
    }

    /**
     * Valida se todos os arquivos necessários foram enviados
     */
    private function validarArquivosEnviados($arquivos)
    {
        $arquivosEsperados = array_keys($this->getConfiguracaoArquivos());
        $arquivosEnviados = collect($arquivos)->map(fn($f) => strtolower($f->getClientOriginalName()))->toArray();
        
        $arquivosFaltantes = array_diff($arquivosEsperados, $arquivosEnviados);
        
        if (!empty($arquivosFaltantes)) {
            throw new \Exception('Arquivos faltando: ' . implode(', ', $arquivosFaltantes));
        }

        if (count($arquivos) !== count($arquivosEsperados)) {
            throw new \Exception('Número incorreto de arquivos. Esperado: ' . count($arquivosEsperados) . ', Enviado: ' . count($arquivos));
        }
    }

    /**
     * Valida as colunas de um arquivo específico
     */
    private function validarColunasArquivo($arquivo, $nomeEsperado, $colunasObrigatorias)
    {
        try {
            $spreadsheet = IOFactory::load($arquivo->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            
            // Obtém o intervalo de células usado
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
            // Verifica se há dados
            if ($highestRow < 2) {
                throw new \Exception('Arquivo vazio');
            }

            // Obtém o cabeçalho
            $cabecalho = [];
            foreach (range('A', $highestColumn) as $col) {
                $valor = trim($sheet->getCell($col . '1')->getValue());
                if (!empty($valor)) {
                    $cabecalho[] = strtolower($valor);
                }
            }

            // Verifica colunas obrigatórias
            $colunasFaltantes = [];
            foreach ($colunasObrigatorias as $coluna) {
                if (!in_array(strtolower($coluna), $cabecalho)) {
                    $colunasFaltantes[] = $coluna;
                }
            }

            if (!empty($colunasFaltantes)) {
                throw new \Exception('Colunas faltando: ' . implode(', ', $colunasFaltantes) . '. Colunas encontradas: ' . implode(', ', $cabecalho));
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception("Erro ao validar arquivo {$nomeEsperado}: " . $e->getMessage());
        }
    }

    /**
     * Processa e salva os dados de um arquivo no banco
     */
    private function processarESalvarArquivo($arquivo, $nomeArquivo, $config)
    {
        try {
            $this->logger->gravacaoArquivo($nomeArquivo);
            
            // Verificar se é um caminho de arquivo ou objeto de upload
            $caminhoArquivo = is_string($arquivo) ? $arquivo : $arquivo->getRealPath();
            $spreadsheet = IOFactory::load($caminhoArquivo);
            $sheet = $spreadsheet->getActiveSheet();
            $dados = $sheet->toArray(null, true, true, true);
            
            // Log do cabeçalho
            $cabecalho = array_shift($dados);

            // Mapeia as colunas do cabeçalho para índices
            $colunas = [];
            foreach ($cabecalho as $indice => $nome) {
                $colunas[strtolower(trim($nome))] = $indice;
            }
            
            // Cabeçalhos mapeados com sucesso
            
            $totalRegistros = 0;
            $registrosAtualizados = 0;
            $registrosInseridos = 0;
            $erros = [];
            
            // Processa cada linha
            foreach ($dados as $index => $linha) {
                try {
                    $model = new $config['model']();
                    
                    // Mapeia os dados de acordo com o tipo de arquivo
                    switch ($nomeArquivo) {
                        case 'derpr_composicoes.xlsx':
                            // Validação dos dados obrigatórios
                            if (empty($linha[$colunas['codigo']])) {
                                throw new \Exception("Campo 'codigo' é obrigatório e não pode ser nulo");
                            }
                            if (empty($linha[$colunas['descricao']])) {
                                throw new \Exception("Campo 'descricao' é obrigatório e não pode ser nulo");
                            }
                            if (empty($linha[$colunas['unidade']])) {
                                throw new \Exception("Campo 'unidade' é obrigatório e não pode ser nulo");
                            }
                            if (empty($linha[$colunas['custo_unitario']])) {
                                throw new \Exception("Campo 'custo_unitario' é obrigatório e não pode ser nulo");
                            }

                            $model->grupo              = trim($linha[$colunas['grupo']]);
                            $model->data_base          = \Carbon\Carbon::hasFormat($linha[$colunas['data_base']], 'd/m/Y')
                                                        ? \Carbon\Carbon::createFromFormat('d/m/Y', trim($linha[$colunas['data_base']]))->format('Y-m-d')
                                                        : null;
                            $model->desoneracao        = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->codigo             = trim($linha[$colunas['codigo']]);
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->unidade            = trim($linha[$colunas['unidade']]);
                            $model->custo_execucao     = $this->paraDecimal($linha[$colunas['custo_execucao']]);
                            $model->custo_material     = $this->paraDecimal($linha[$colunas['custo_material']]);
                            $model->custo_sub_servico  = $this->paraDecimal($linha[$colunas['custo_sub_servico']]);
                            $model->custo_unitario     = $this->paraDecimal($linha[$colunas['custo_unitario']]);
                            $model->transporte          = trim($linha[$colunas['transporte']]);
                            break;

                        case 'derpr_equipamentos.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $data_br = trim($linha[$colunas['data_base']]);
                            $data_obj = \Carbon\Carbon::createFromFormat('d/m/Y', $data_br);
                            if ($data_obj === false) {
                                throw new \Exception("Data inválida: '{$data_br}'. Formato esperado: DD/MM/YYYY");
                            }
                            $model->data_base = $data_obj->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo_equipamento = trim($linha[$colunas['codigo_equipamento']]);
                            $model->quantidade = (float) str_replace(',', '.', $linha[$colunas['quantidade']]);
                            $model->ut_produtiva = $this->paraDecimal($linha[$colunas['ut_produtiva']]);
                            $model->ut_improdutiva = $this->paraDecimal($linha[$colunas['ut_improdutiva']]);
                            $model->vl_hr_prod = $this->paraDecimal($linha[$colunas['vl_hr_prod']]);
                            $model->vl_hr_imp = $this->paraDecimal($linha[$colunas['vl_hr_imp']]);
                            $model->custo_horario = $this->paraDecimal($linha[$colunas['custo_horario']]);
                            break;
                        
                                                
                        case 'derpr_mao_de_obra.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $data_br = trim($linha[$colunas['data_base']]);
                            $data_obj = \Carbon\Carbon::createFromFormat('d/m/Y', $data_br);
                            if ($data_obj === false) {
                                throw new \Exception("Data inválida: '{$data_br}'. Formato esperado: DD/MM/YYYY");
                            }
                            $model->data_base = $data_obj->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->eq_salarial = $this->paraDecimal($linha[$colunas['eq_salarial']]);
                            $model->encargos_percentagem = $this->paraDecimal($linha[$colunas['encargos_percentagem']]);
                            $model->sal_hora = $this->paraDecimal($linha[$colunas['sal_hora']]);
                            $model->consumo = (float) str_replace(',', '.', $linha[$colunas['consumo']]);
                            $model->custo_horario = $this->paraDecimal($linha[$colunas['custo_horario']]);
                            break;

                        case 'derpr_itens_incidencia.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $data_br = trim($linha[$colunas['data_base']]);
                            $data_obj = \Carbon\Carbon::createFromFormat('d/m/Y', $data_br);
                            if ($data_obj === false) {
                                throw new \Exception("Data inválida: '{$data_br}'. Formato esperado: DD/MM/YYYY");
                            }
                            $model->data_base = $data_obj->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->percentagem = $this->paraDecimal($linha[$colunas['percentagem']]);
                            $model->tem_mo = strtolower(trim($linha[$colunas['tem_mo']])) === 'verdadeiro' ? 'sim' : 'nao';
                            $model->custo = $this->paraDecimal($linha[$colunas['custo']]);
                            break;

                        case 'derpr_materiais.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $data_br = trim($linha[$colunas['data_base']]);
                            $data_obj = \Carbon\Carbon::createFromFormat('d/m/Y', $data_br);
                            if ($data_obj === false) {
                                throw new \Exception("Data inválida: '{$data_br}'. Formato esperado: DD/MM/YYYY");
                            }
                            $model->data_base = $data_obj->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->unidade = trim($linha[$colunas['unid']]);
                            $model->custo_unitario = $this->paraDecimal($linha[$colunas['custo_unitario']]);
                            $model->consumo = (float) str_replace(',', '.', $linha[$colunas['consumo']]);
                            $model->custo_unitario_final = $this->paraDecimal($linha[$colunas['custo_unitario_final']]);
                            break;

                        case 'derpr_servicos.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $data_br = trim($linha[$colunas['data_base']]);
                            $data_obj = \Carbon\Carbon::createFromFormat('d/m/Y', $data_br);
                            if ($data_obj === false) {
                                throw new \Exception("Data inválida: '{$data_br}'. Formato esperado: DD/MM/YYYY");
                            }
                            $model->data_base = $data_obj->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->unidade = trim($linha[$colunas['unid']]);
                            $model->custo_unitario = $this->paraDecimal($linha[$colunas['custo_unitario']]);
                            $model->consumo = (float) str_replace(',', '.', $linha[$colunas['consumo']]);
                            $model->custo_unitario_final = $this->paraDecimal($linha[$colunas['custo_unitario_final']]);
                            break;
                            
                        case 'derpr_transportes.xlsx':
                            // Detectar e converter data automaticamente
                            $data_input = trim($linha[$colunas['data_base']]);
                            
                            // Tentar primeiro formato ISO (Y-m-d)
                            $data_obj = \DateTime::createFromFormat('Y-m-d', $data_input);
                            if ($data_obj === false) {
                                // Se falhar, tentar formato brasileiro (d/m/Y)
                                $data_obj = \DateTime::createFromFormat('d/m/Y', $data_input);
                                if ($data_obj === false) {
                                    throw new \Exception("Data inválida: '{$data_input}'. Formatos aceitos: YYYY-MM-DD ou DD/MM/YYYY");
                                }
                            }
                            
                            $data_iso = $data_obj->format('Y-m-d');
                            $model->data_base = $data_iso;
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $model->descricao = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->unidade = trim($linha[$colunas['unid']]);
                            $model->formula1 = trim($linha[$colunas['formula1']]);
                            $model->formula2 = trim($linha[$colunas['formula2']]);
                            $model->custo = $this->paraDecimal($linha[$colunas['custo']]);
                            $model->consumo = (float) str_replace(',', '.', $linha[$colunas['consumo']]);
                            $model->custo_unitario = $this->paraDecimal($linha[$colunas['custo_unitario']]);
                            break;
                            
                        case 'formulas_transporte.xlsx':
                            // Detectar e converter data automaticamente
                            $data_input = trim($linha[$colunas['data_base']]);
                            
                            // Tentar primeiro formato ISO (Y-m-d)
                            $data_obj = \DateTime::createFromFormat('Y-m-d', $data_input);
                            if ($data_obj === false) {
                                // Se falhar, tentar formato brasileiro (d/m/Y)
                                $data_obj = \DateTime::createFromFormat('d/m/Y', $data_input);
                                if ($data_obj === false) {
                                    throw new \Exception("Data inválida: '{$data_input}'. Formatos aceitos: YYYY-MM-DD ou DD/MM/YYYY");
                                }
                            }
                            
                            $data_iso = $data_obj->format('Y-m-d');
                            $model->data_base = $data_iso;
                            $model->desoneracao = strtolower(trim($linha[$colunas['desoneracao']])) === 'com' ? 'com' : 'sem';
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->descricao = trim($linha[$colunas['descricao']]);
                            $model->unidade = trim($linha[$colunas['unidade']]);
                            $model->formula_transporte = trim($linha[$colunas['formula_transporte']]);
                            break;
                    }

                    // Verifica se o registro já existe
                    $registroExistente = null;
                    switch ($nomeArquivo) {
                        case 'derpr_composicoes.xlsx':
                            $registroExistente = $config['model']::where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'derpr_equipamentos.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo_equipamento', $model->codigo_equipamento)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'derpr_mao_de_obra.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'derpr_itens_incidencia.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'derpr_materiais.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'derpr_servicos.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                            
                        case 'derpr_transportes.xlsx':
                            $registroExistente = $config['model']::where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                            
                        case 'formulas_transporte.xlsx':
                            $registroExistente = $config['model']::where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                    }

                    if ($registroExistente) {
                        // Atualiza o registro existente
                        $registroExistente->update($model->toArray());
                        $registrosAtualizados++;
                        $totalRegistros++;
                    } else {
                        // Insere novo registro
                        $model->save();
                        $registrosInseridos++;
                        $totalRegistros++;
                    }

                } catch (\Exception $e) {
                    $erros[] = [
                        'linha' => $index + 2,
                        'erro' => $e->getMessage(),
                        'dados' => $linha,
                        'colunas_mapeadas' => $colunas
                    ];
                    
                    // Erro encontrado na linha
                }
            }

            if (!empty($erros)) {
                throw new \Exception("Erros encontrados: " . json_encode($erros));
            }

            return [
                'total_registros' => $totalRegistros,
                'registros_atualizados' => $registrosAtualizados,
                'registros_inseridos' => $registrosInseridos,
                'erros' => $erros
            ];
        } catch (\Exception $e) {
            throw new \Exception("Erro ao processar arquivo {$nomeArquivo}: " . $e->getMessage());
        }
    }

    /**
     * Salva dados de fórmulas de transporte em Excel
     * 
     * @param array $dados
     * @return void
     */
    private function salvarDadosFormulasTransporteEmExcel(array $dados): void
    {
        try {
            // Obter diretório da sessão
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                throw new \Exception('Diretório de processamento não encontrado na sessão');
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            // Criar diretório se não existir
            if (!file_exists($diretorioProcessamento)) {
                mkdir($diretorioProcessamento, 0755, true);
            }

            // Criar planilha
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Definir cabeçalhos
            $headers = [
                'data_base', 'desoneracao', 'codigo', 'descricao', 
                'unidade', 'formula_transporte'
            ];
            
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1', $header);
                $col++;
            }

            // Adicionar dados
            $row = 2;
            foreach ($dados as $item) {
                $col = 1;
                foreach ($headers as $header) {
                    $value = $item[$header] ?? '';
                    $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, $value);
                    $col++;
                }
                $row++;
            }

            // Salvar arquivo
            $caminhoArquivo = $diretorioProcessamento . '/formulas_transporte.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($caminhoArquivo);

            // Gerar metadata
            $metadata = [
                'arquivo_original' => 'PDF processado',
                'data_processamento' => now()->toISOString(),
                'total_registros' => count($dados),
                'usuario' => auth()->user() ? auth()->user()->name : 'N/A',
                'ip' => request()->ip()
            ];

            $arquivoMetadata = $diretorioProcessamento . '/derpr_metadata_formulas_transporte.json';
            file_put_contents($arquivoMetadata, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Arquivo Excel de fórmulas de transporte salvo com sucesso

        } catch (\Exception $e) {
            $this->logErroCritico('SALVAR_EXCEL_FORMULAS', $e->getMessage());
        }
    }

    // Métodos de log removidos - agora usando service de log

    public function paraDecimal(?string $valor): ?float
    {
        if (is_null($valor)) return null;

        // Remove espaços, "R$", etc.
        $valor = trim($valor);
        $valor = str_replace(['R$', ' '], '', $valor);

        // Remove pontos de milhar (.) e troca vírgula decimal (,) por ponto
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);

        // Verifica se é um número válido
        return is_numeric($valor) ? (float)$valor : null;
     }

    /**
     * Método de teste para verificar o sistema de logs
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testarLogs()
    {
        try {
            // Teste 1: Verificar se o usuário está autenticado
            $user = auth()->user();
            $authCheck = auth()->check();
            
            // Teste 2: Verificar informações da sessão
            $sessionId = session()->getId();
            $sessionData = session()->all();
            
            // Teste 3: Verificar se há token JWT
            $request = request();
            $authorization = $request->header('Authorization');
            $bearerToken = $request->bearerToken();
            
            // Teste 4: Verificar cookies
            $cookies = $request->cookies->all();
            
            // Log de teste
            $this->logger->inicioOperacao('TESTE_LOGS', [
                'usuario_id' => $user ? $user->id : 'N/A',
                'usuario_nome' => $user ? $user->name : 'N/A',
                'autenticado' => $authCheck ? 'Sim' : 'Não',
                'session_id' => $sessionId,
                'tem_authorization' => $authorization ? 'Sim' : 'Não',
                'tem_bearer_token' => $bearerToken ? 'Sim' : 'Não'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Teste de logs executado',
                'dados_autenticacao' => [
                    'usuario' => $user ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ] : null,
                    'auth_check' => $authCheck,
                    'session_id' => $sessionId,
                    'authorization_header' => $authorization,
                    'bearer_token' => $bearerToken ? 'Presente' : 'Ausente',
                    'cookies' => array_keys($cookies)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao testar: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
} 
