<?php

namespace App\Http\Controllers\Api\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;


class ImportarSinapiController extends Controller
{
    /**
     * Verifica se o usuário tem permissão para acessar este controller
     */
    private function verificarPermissao()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user->isSuperAdmin() && !$user->hasRole('gerenciar_importacao_sinapi')) {
            abort(403, 'Acesso negado. Permissão insuficiente.');
        }
    }

    /**
     * Processa arquivo Excel de composições e insumos SINAPI
     * 
     * OBJETIVO: Receber um arquivo Excel com 5 abas (ISD, ICD, CSD, CCD, Analítico)
     * e processá-lo via script Python para extrair apenas as colunas relevantes,
     * gerando 5 arquivos separados para cada tipo de dado.
     * 
     * FLUXO:
     * 1. Validação do arquivo (.xlsx, tamanho)
     * 2. Limpeza de diretórios anteriores
     * 3. Salvamento temporário em pasta específica
     * 4. Execução do script Python que processa cada aba
     * 5. Geração de 5 arquivos processados + metadados
     * 6. Limpeza do arquivo temporário original
     * 
     * @param Request $request Requisição HTTP contendo o arquivo Excel
     * @return \Illuminate\Http\JsonResponse
     *         - success: true/false
     *         - message: Descrição do resultado
     *         - saida: Nome do diretório com arquivos processados
     *         - metadados: Informações do processamento (mês, data, abas)
     */
    public function processarComposicoesInsumos(Request $request)
    {
        $this->verificarPermissao();
        
        try {
            // ===================================================================
            // ETAPA 1: LOG DE INÍCIO E VALIDAÇÃO
            // ===================================================================
            
            $this->logInicioOperacao('PROCESSAMENTO_COMPOSICOES_INSUMOS', [
                'arquivo' => $request->file('arquivo')->getClientOriginalName(),
                'tamanho' => $request->file('arquivo')->getSize()
            ]);
            
            if (!$request->hasFile('arquivo')) {
                $this->logErroValidacao('VALIDACAO_ARQUIVO', 'Nenhum arquivo enviado');
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum arquivo enviado'
                ], 400);
            }

            $arquivo = $request->file('arquivo');

            // Validar extensão (.xlsx obrigatório)
            if ($arquivo->getClientOriginalExtension() !== 'xlsx') {
                $this->logErroValidacao('VALIDACAO_ARQUIVO', 'Arquivo deve ter extensão .xlsx');
                return response()->json([
                    'success' => false,
                    'message' => 'O arquivo deve ter extensão .xlsx'
                ], 400);
            }
            
            $this->logProgresso('VALIDACAO_ARQUIVO', 'Arquivo Excel validado com sucesso');
            
            // ===================================================================
            // ETAPA 2: LIMPEZA DE DIRETÓRIOS ANTERIORES
            // ===================================================================
            
            $this->logProgresso('LIMPEZA_DIRETORIOS', 'Iniciando limpeza de diretórios anteriores');
            
                    // Verificar e apagar diretórios anteriores de processamento SINAPI
        $tempDir = storage_path('temp');
        if (is_dir($tempDir)) {
                $subdirs = glob($tempDir . '/processado_sinapi_*', GLOB_ONLYDIR);
                foreach ($subdirs as $subdir) {
                    // Remover diretório e todo seu conteúdo
                    $this->removeDirectory($subdir);
                }
            }
            
            $this->logProgresso('LIMPEZA_DIRETORIOS', 'Diretórios anteriores removidos com sucesso');
            
            // ===================================================================
            // ETAPA 3: SALVAMENTO TEMPORÁRIO E CRIAÇÃO DE DIRETÓRIO ÚNICO
            // ===================================================================
            
            $this->logProgresso('ARQUIVO_TEMPORARIO', 'Salvando arquivo temporariamente');
            
            // Salvar arquivo temporariamente (usando o mesmo padrão do DER-PR)
            $nomeTemporario = uniqid('entrada_') . '.xlsx';
            $caminhoRelativoEntrada = $arquivo->storeAs('temp', $nomeTemporario);
            $caminhoAbsolutoEntrada = Storage::path($caminhoRelativoEntrada);
            
            // Criar diretório único para arquivos processados (usando o mesmo padrão do DER-PR)
            $nomeSaidaDir = 'processado_sinapi_' . uniqid();
            $diretorioAbsolutoSaida = Storage::path('temp/' . $nomeSaidaDir);
            
            // Criar diretório usando mkdir para evitar problemas de aninhamento
            if (!is_dir($diretorioAbsolutoSaida)) {
                mkdir($diretorioAbsolutoSaida, 0755, true);
            }
            
            // Armazenar nome do diretório na session para uso nas outras abas
            session(['sinapi_processamento_dir' => $nomeSaidaDir]);
            
            $this->logProgresso('DIRETORIO_CRIADO', 'Diretório de processamento criado', ['diretorio' => $nomeSaidaDir]);
            
            // ===================================================================
            // ETAPA 4: EXECUÇÃO DO SCRIPT PYTHON
            // ===================================================================
            
            $this->logProgresso('SCRIPT_PYTHON', 'Executando script Python para processamento');
            
            // Usar o diretório já criado anteriormente (usando o mesmo padrão do DER-PR)
            $diretorioAbsolutoSaida = Storage::path('temp/' . $nomeSaidaDir);
            
            // Verificar se o arquivo de entrada existe antes de executar o script
            if (!file_exists($caminhoAbsolutoEntrada)) {
                throw new \Exception("Arquivo de entrada não encontrado: " . $caminhoAbsolutoEntrada);
            }
            
            $this->logProgresso('SCRIPT_PYTHON_VERIFICACAO', 'Arquivo de entrada encontrado: ' . $caminhoAbsolutoEntrada);
            
            // Executar script Python com diretório de saída
            $comando = "python " . base_path("01_python/importacao_SINAPI/01.Importar-SINAPI-Tabela-Servicos.py") . " " . escapeshellarg($caminhoAbsolutoEntrada) . " " . escapeshellarg($diretorioAbsolutoSaida);
            
            $this->logProgresso('SCRIPT_PYTHON_COMANDO', 'Comando Python: ' . $comando);
            
            // Executar script Python usando exec para capturar código de retorno
            $saida = '';
            $codigoRetorno = 0;
            exec($comando . " 2>&1", $saida, $codigoRetorno);
            
            $this->logProgresso('SCRIPT_PYTHON_CODIGO_RETORNO', 'Código de retorno: ' . $codigoRetorno);
            $this->logProgresso('SCRIPT_PYTHON_SAIDA', 'Saída do script Python: ' . var_export($saida, true));
            
            // Verificar se o script executou com sucesso (0 = sucesso, 1 = erro)
            if ($codigoRetorno !== 0) {
                throw new \Exception("Script Python retornou erro: " . $codigoRetorno . " - Saída: " . implode("\n", $saida));
            }
            
            $this->logProgresso('SCRIPT_PYTHON', 'Script Python executado com sucesso');
            
            // ===================================================================
            // ETAPA 5: LIMPEZA E FINALIZAÇÃO
            // ===================================================================
            
            // Limpar arquivo temporário
            Storage::delete($caminhoRelativoEntrada);
            $this->logProgresso('LIMPEZA', 'Arquivo temporário removido');
            
            // Armazenar informações para auditoria
            $auditoriaInfo = [
                'diretorio_session' => $nomeSaidaDir,
                'tamanho' => $arquivo->getSize(),
                'usuario' => auth()->user() ? auth()->user()->name : 'N/A'
            ];
            
            $this->logSucesso('PROCESSAMENTO_COMPOSICOES_INSUMOS', [
                'diretorio_processamento' => $nomeSaidaDir,
                'tamanho_arquivo_original' => $arquivo->getSize()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Arquivo processado com sucesso',
                'saida' => $nomeSaidaDir,
                'metadados' => $auditoriaInfo
            ]);
            
        } catch (\Exception $e) {
            $this->logErroCritico('PROCESSAMENTO_COMPOSICOES_INSUMOS', $e->getMessage(), [
                'arquivo' => $request->hasFile('arquivo') ? $request->file('arquivo')->getClientOriginalName() : 'N/A'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processa arquivo de percentagens de mão de obra SINAPI
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarPercentagensMaoDeObra(Request $request)
    {
        $this->verificarPermissao();
        
        try {
            // ===================================================================
            // ETAPA 1: LOG DE INÍCIO E VALIDAÇÃO
            // ===================================================================
            
            $this->logInicioOperacao('PROCESSAMENTO_PERCENTAGENS_MAO_DE_OBRA', [
                'arquivo' => $request->file('arquivo')->getClientOriginalName(),
                'tamanho' => $request->file('arquivo')->getSize()
            ]);
            
            if (!$request->hasFile('arquivo')) {
                $this->logErroValidacao('VALIDACAO_ARQUIVO', 'Nenhum arquivo enviado');
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum arquivo enviado'
                ], 400);
            }

            $arquivo = $request->file('arquivo');

            // Validar extensão (.xlsx obrigatório)
            if ($arquivo->getClientOriginalExtension() !== 'xlsx') {
                $this->logErroValidacao('VALIDACAO_ARQUIVO', 'Arquivo deve ter extensão .xlsx');
                return response()->json([
                    'success' => false,
                    'message' => 'O arquivo deve ter extensão .xlsx'
                ], 400);
            }
            
            $this->logProgresso('VALIDACAO_ARQUIVO', 'Arquivo Excel validado com sucesso');
            
            // ===================================================================
            // ETAPA 2: DIRETÓRIO DE PROCESSAMENTO
            // ===================================================================
            
            $this->logProgresso('VERIFICACAO_DIRETORIO', 'Verificando diretório de processamento');
            
            // Obter diretório de processamento da sessão (criado pela Aba 1)
            $nomeDiretorio = session('sinapi_processamento_dir');
            
            if (!$nomeDiretorio) {
                            // Fallback: procurar diretório mais recente (usando o mesmo padrão da Aba 1)
            $diretorios = glob(Storage::path('temp/processado_sinapi_*'));
            if (empty($diretorios)) {
                $this->logErroValidacao('VERIFICACAO_DIRETORIO', 'Nenhum diretório de processamento encontrado');
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum diretório de processamento encontrado. Execute primeiro o processamento da Aba 1.'
                ], 400);
            }
            $nomeDiretorio = basename(end($diretorios));
        }
        
        $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            if (!is_dir($diretorioProcessamento)) {
                $this->logErroValidacao('VERIFICACAO_DIRETORIO', 'Diretório de processamento não encontrado no sistema');
                return response()->json([
                    'success' => false,
                    'message' => 'Diretório de processamento não encontrado: ' . $nomeDiretorio
                ], 400);
            }
            
            $this->logProgresso('VERIFICACAO_DIRETORIO', 'Diretório de processamento validado', ['diretorio' => $nomeDiretorio]);
            
            // ===================================================================
            // ETAPA 3: PROCESSAMENTO DIRETO NO PHP
            // ===================================================================
            
            $this->logProgresso('PROCESSAMENTO_EXCEL', 'Iniciando processamento do arquivo Excel');
            
            // Carregar arquivo Excel
            $spreadsheetEntrada = IOFactory::load($arquivo->getRealPath());
            
            // Processar cada aba
            $resultados = [];
            $abasProcessadas = [];
            
            foreach ($spreadsheetEntrada->getSheetNames() as $nomeAba) {
                // Verificar se é uma aba de mão de obra
                if (!str_contains(strtoupper($nomeAba), 'SEM') && !str_contains(strtoupper($nomeAba), 'COM')) {
                    continue;
                }
                
                $this->logProgresso('PROCESSAMENTO_ABA', "Processando aba: {$nomeAba}");
                
                $sheetEntrada = $spreadsheetEntrada->getSheetByName($nomeAba);
                $dados = $sheetEntrada->toArray();
                
                // Determinar tipo de desoneração
                $desoneracao = str_contains(strtoupper($nomeAba), 'COM') ? 'com' : 'sem';
                
                // Extrair datas das células B2 e B4
                $mesReferencia = $dados[1][1] ?? null; // B2
                $dataEmissao = $dados[3][1] ?? null; // B4
                
                // Processar data_base
                $dataBase = null;
                if ($mesReferencia) {
                    if (is_numeric($mesReferencia)) {
                        $dataBase = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mesReferencia))->startOfMonth();
                    } elseif (preg_match('/(\d{2}\/\d{4})/', $mesReferencia, $matches)) {
                        $dataBase = Carbon::createFromFormat('m/Y', $matches[1])->startOfMonth();
                    }
                }
                
                // Processar data_emissao
                $dataEmissaoFormatada = null;
                if ($dataEmissao) {
                    if (is_numeric($dataEmissao)) {
                        $dataEmissaoFormatada = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dataEmissao));
                    } elseif (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $dataEmissao, $matches)) {
                        $dataEmissaoFormatada = Carbon::createFromFormat('d/m/Y', $matches[1]);
                    }
                }
                
                $registros = [];
                
                // Processar linhas de dados (a partir da linha 7)
                foreach ($dados as $index => $linha) {
                    if ($index < 6 || count($linha) < 22 || empty($linha[1])) {
                        continue;
                    }
                    
                    // Verificar se é linha de cabeçalho ou linha vazia
                    if (isset($linha[0]) && str_starts_with(strtoupper($linha[0]), 'CUSTOS HORÁRIOS')) {
                        continue;
                    }
                    
                    // Extrair valor PR da coluna V (índice 21)
                    $valorPR = $linha[21] ?? null;
                    $percentagem = null;
                    
                    if (is_numeric($valorPR)) {
                        $percentagem = floatval($valorPR);
                        if ($percentagem > 1) $percentagem /= 100;
                    } else {
                        $valorLimpo = str_replace(['%', ' '], '', $valorPR);
                        $valorLimpo = str_replace(',', '.', $valorLimpo);
                        $percentagem = floatval($valorLimpo);
                        if ($percentagem > 1) $percentagem /= 100;
                    }
                    
                    // Validar percentagem
                    if (!is_numeric($percentagem) || $percentagem < 0 || $percentagem > 1) {
                        continue;
                    }
                    
                    $registros[] = [
                        'codigo_composicao' => trim($linha[1]),
                        'descricao' => trim($linha[2]),
                        'unidade' => trim($linha[3]),
                        'percentagem_pr' => $percentagem,
                        'data_emissao' => $dataEmissaoFormatada ? $dataEmissaoFormatada->format('Y-m-d') : null,
                        'data_base' => $dataBase ? $dataBase->format('Y-m-d') : null,
                        'desoneracao' => $desoneracao
                    ];
                }
                
                // Criar arquivo de saída para esta aba
                if (!empty($registros)) {
                    $nomeArquivo = "sinapi_mao_obra_" . strtoupper($desoneracao) . "_DESONERACAO.xlsx";
                    $caminhoArquivo = $diretorioProcessamento . '/' . $nomeArquivo;
                    
                    // Criar novo spreadsheet
                    $spreadsheetSaida = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $spreadsheetSaida->removeSheetByIndex(0); // Remove aba padrão
                    
                    // Criar aba com dados processados
                    $worksheet = $spreadsheetSaida->createSheet();
                    $worksheet->setTitle($nomeAba);
                    
                    // Adicionar cabeçalho
                    $cabecalho = [
                        'codigo_composicao',
                        'descricao',
                        'unidade',
                        'percentagem_pr',
                        'data_emissao',
                        'data_base',
                        'desoneracao'
                    ];
                    $worksheet->fromArray([$cabecalho], null, 'A1');
                    
                    // Adicionar dados
                    $worksheet->fromArray($registros, null, 'A2');
                    
                    // Salvar arquivo
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheetSaida);
                    $writer->save($caminhoArquivo);
                    
                    $resultados[$desoneracao] = count($registros);
                    $abasProcessadas[] = $nomeAba;
                    

                }
            }
            
            // ===================================================================
            // ETAPA 4: GERAR METADADOS
            // ===================================================================
            
            $this->logProgresso('GERACAO_METADADOS', 'Gerando arquivo de metadados');
            
            $metadata = [
                'mes_referencia' => $mesReferencia,
                'data_emissao' => $dataEmissao,
                'abas_processadas' => $abasProcessadas,
                'total_registros' => array_sum($resultados),
                'registros_por_tipo' => $resultados,
                'data_processamento' => now()->toISOString()
            ];
            
            $metadataPath = $diretorioProcessamento . '/sinapi_mao_obra_metadata.json';
            file_put_contents($metadataPath, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            $this->logProgresso('GERACAO_METADADOS', 'Metadados gerados com sucesso');

            // ===================================================================
            // ETAPA 5: RESPOSTA DE SUCESSO
            // ===================================================================
            
            $this->logSucesso('PROCESSAMENTO_PERCENTAGENS_MAO_DE_OBRA', [
                'diretorio_processamento' => $nomeDiretorio,
                'total_abas_processadas' => count($abasProcessadas),
                'total_registros' => array_sum($resultados),
                'abas_processadas' => $abasProcessadas
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Arquivo de mão de obra processado com sucesso! ' . count($abasProcessadas) . ' abas processadas.',
                'saida' => $nomeDiretorio,
                'metadados' => $metadata
            ]);

        } catch (\Exception $e) {
            $this->logErroCritico('PROCESSAMENTO_PERCENTAGENS_MAO_DE_OBRA', $e->getMessage(), [
                'arquivo' => $request->hasFile('arquivo') ? $request->file('arquivo')->getClientOriginalName() : 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar percentagens de mão de obra SINAPI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Faz o download de um arquivo processado específico
     * 
     * OBJETIVO: Permitir o download direto dos arquivos processados pelo script Python.
     * Os arquivos são localizados no diretório específico da session e servidos como resposta HTTP.
     * 
     * @param string $tipo Tipo do arquivo (isd, icd, csd, ccd, analitico)
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function downloadArquivoProcessado(string $tipo)
    {
        $this->verificarPermissao();
        
        try {
            // Validar tipo de arquivo
            $tiposValidos = ['isd', 'icd', 'csd', 'ccd', 'analitico'];
            if (!in_array($tipo, $tiposValidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de arquivo inválido. Tipos válidos: ' . implode(', ', $tiposValidos)
                ], 400);
            }

            // Obter diretório de processamento da session
            $nomeDiretorio = session('sinapi_processamento_dir');
            
            // Se não encontrou na session, procurar o diretório mais recente
            if (!$nomeDiretorio) {
                $this->logProgresso('DOWNLOAD', 'Session sinapi_processamento_dir não encontrada, procurando diretório mais recente', [
                    'session_id' => session()->getId()
                ]);
                
                // Procurar diretório mais recente de processamento SINAPI (usando o mesmo padrão da Aba 1)
                $tempDir = Storage::path('temp');
                if (is_dir($tempDir)) {
                    $subdirs = glob($tempDir . '/processado_sinapi_*', GLOB_ONLYDIR);
                    if (!empty($subdirs)) {
                        // Ordenar por data de modificação (mais recente primeiro)
                        usort($subdirs, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        
                        $nomeDiretorio = basename($subdirs[0]);

                    }
                }
            }
            
            if (!$nomeDiretorio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum processamento encontrado. Execute o processamento da Aba 1 primeiro.'
                ], 404);
            }

            // Mapear nomes de arquivo por tipo (como gerado pelo script Python)
            $nomesArquivos = [
                'isd' => 'sinapi_processado_ISD.xlsx',
                'icd' => 'sinapi_processado_ICD.xlsx',
                'csd' => 'sinapi_processado_CSD.xlsx',
                'ccd' => 'sinapi_processado_CCD.xlsx',
                'analitico' => 'sinapi_processado_Analítico.xlsx'
            ];

            $nomeArquivo = $nomesArquivos[$tipo];
            $caminhoArquivo = Storage::path('temp/' . $nomeDiretorio . '/' . $nomeArquivo);

            // Verificar se o arquivo existe
            if (!file_exists($caminhoArquivo)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo processado não encontrado. Execute o processamento primeiro.'
                ], 404);
            }



            // Retornar arquivo para download
            return response()->download($caminhoArquivo, $nomeArquivo, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer download do arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Faz o download de um arquivo de mão de obra processado específico
     * 
     * OBJETIVO: Permitir o download direto dos arquivos de percentagens de mão de obra
     * processados pelo script Python. Os arquivos são localizados no diretório específico
     * da session e servidos como resposta HTTP.
     * 
     * @param string $tipo Tipo do arquivo (sem_desoneracao, com_desoneracao)
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function downloadArquivoProcessadoMaoDeObra(string $tipo)
    {
        $this->verificarPermissao();
        
        try {
            // Validar tipo de arquivo
            $tiposValidos = ['sem_desoneracao', 'com_desoneracao'];
            if (!in_array($tipo, $tiposValidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de arquivo inválido. Tipos válidos: ' . implode(', ', $tiposValidos)
                ], 400);
            }

            // Obter diretório de processamento da session
            $nomeDiretorio = session('sinapi_processamento_dir');
            
            // Se não encontrou na session, procurar o diretório mais recente
            if (!$nomeDiretorio) {
                
                // Procurar diretório mais recente de processamento SINAPI
                $tempDir = storage_path('temp');
                if (is_dir($tempDir)) {
                    $subdirs = glob($tempDir . '/processado_sinapi_*', GLOB_ONLYDIR);
                    if (!empty($subdirs)) {
                        // Ordenar por data de modificação (mais recente primeiro)
                        usort($subdirs, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        
                        $nomeDiretorio = basename($subdirs[0]);

                    }
                }
            }
            
            if (!$nomeDiretorio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum processamento encontrado. Execute o processamento da Aba 1 primeiro.'
                ], 404);
            }

            // Mapear nomes de arquivo por tipo (como gerado pelo script Python)
            $nomesArquivos = [
                'sem_desoneracao' => 'sinapi_mao_obra_SEM_DESONERACAO.xlsx',
                'com_desoneracao' => 'sinapi_mao_obra_COM_DESONERACAO.xlsx'
            ];

            $nomeArquivo = $nomesArquivos[$tipo];
            $caminhoArquivo = Storage::path('temp/' . $nomeDiretorio . '/' . $nomeArquivo);

            // Verificar se o arquivo existe
            if (!file_exists($caminhoArquivo)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo de mão de obra processado não encontrado. Execute o processamento da Aba 2 primeiro.'
                ], 404);
            }



            // Retornar arquivo para download
            return response()->download($caminhoArquivo, $nomeArquivo, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer download do arquivo de mão de obra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grava dados SINAPI no banco de dados
     * 
     * OBJETIVO: Detectar automaticamente os arquivos processados nas Abas 1 e 2
     * e inserir os dados no banco de dados do sistema OrçaCidade.
     * 
     * FLUXO:
     * 1. Detectar diretório de processamento (session ou mais recente)
     * 2. Validar existência dos 7 arquivos necessários
     * 3. Ler e processar cada arquivo
     * 4. Gravar dados no banco de dados
     * 5. Retornar estatísticas do processamento
     * 
     * @param Request $request Requisição HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    public function gravar(Request $request)
    {
        $this->verificarPermissao();
        
        try {
            // ===================================================================
            // ETAPA 1: DETECÇÃO DO DIRETÓRIO DE PROCESSAMENTO
            // ===================================================================
            
            // Obter diretório de processamento da session
            $nomeDiretorio = session('sinapi_processamento_dir');
            
            // Se não encontrou na session, procurar o diretório mais recente
            if (!$nomeDiretorio) {
                
                
                // Procurar diretório mais recente de processamento SINAPI
                $tempDir = storage_path('temp');
                if (is_dir($tempDir)) {
                    $subdirs = glob($tempDir . '/processado_sinapi_*', GLOB_ONLYDIR);
                    if (!empty($subdirs)) {
                        // Ordenar por data de modificação (mais recente primeiro)
                        usort($subdirs, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        
                        $nomeDiretorio = basename($subdirs[0]);

                    }
                }
            }
            
            if (!$nomeDiretorio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum processamento encontrado. Execute o processamento das Abas 1 e 2 primeiro.'
                ], 400);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            if (!is_dir($diretorioProcessamento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diretório de processamento não encontrado. Execute o processamento das Abas 1 e 2 primeiro.'
                ], 400);
            }

            // ===================================================================
            // ETAPA 2: VALIDAÇÃO DOS ARQUIVOS NECESSÁRIOS
            // ===================================================================
            
            // Lista de arquivos esperados (7 arquivos principais)
            $arquivosEsperados = [
                // Arquivos da Aba 1 (Composições e Insumos)
                'sinapi_processado_ISD.xlsx',
                'sinapi_processado_ICD.xlsx', 
                'sinapi_processado_CSD.xlsx',
                'sinapi_processado_CCD.xlsx',
                'sinapi_processado_Analítico.xlsx',
                // Arquivos da Aba 2 (Mão de Obra)
                'sinapi_mao_obra_SEM_DESONERACAO.xlsx',
                'sinapi_mao_obra_COM_DESONERACAO.xlsx'
            ];
            
            // Verificar existência de cada arquivo
            $arquivosEncontrados = [];
            $arquivosFaltantes = [];
            
            foreach ($arquivosEsperados as $arquivo) {
                $caminhoArquivo = $diretorioProcessamento . '/' . $arquivo;
                if (file_exists($caminhoArquivo)) {
                    $arquivosEncontrados[] = [
                        'nome' => $arquivo,
                        'caminho' => $caminhoArquivo,
                        'tamanho' => filesize($caminhoArquivo),
                        'data_modificacao' => date('Y-m-d H:i:s', filemtime($caminhoArquivo))
                    ];
                } else {
                    $arquivosFaltantes[] = $arquivo;
                }
            }
            
            // Log da validação
            $this->logSINAPI('info', 'VALIDACAO', 'Validação de arquivos SINAPI para gravação', [
                'diretorio' => $nomeDiretorio,
                'total_esperados' => count($arquivosEsperados),
                'total_encontrados' => count($arquivosEncontrados),
                'arquivos_encontrados' => array_column($arquivosEncontrados, 'nome'),
                'arquivos_faltantes' => $arquivosFaltantes
            ]);
            
            // Verificar se todos os arquivos foram encontrados
            if (!empty($arquivosFaltantes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivos faltantes: ' . implode(', ', $arquivosFaltantes) . '. Execute o processamento das Abas 1 e 2 primeiro.',
                    'arquivos_faltantes' => $arquivosFaltantes,
                    'arquivos_encontrados' => array_column($arquivosEncontrados, 'nome')
                ], 400);
            }

            // ===================================================================
            // ETAPA 3: PROCESSAMENTO E GRAVAÇÃO NO BANCO
            // ===================================================================
            
            $inicioProcessamento = microtime(true);
            $resultados = [];
            $detalhesProcessamento = [];
            
            $this->logInicioGravacao($arquivosEncontrados, $request);
            
            // Iniciar transação
            DB::beginTransaction();
            
            try {
                // Passo 1: Gravar Mão de Obra
                $this->logProcessamentoArquivo('Mão de Obra', 'INICIANDO');
                $resultados['mao_de_obra'] = $this->gravarMaoDeObra($diretorioProcessamento);
                $detalhesProcessamento[] = "Mão de Obra: " . $resultados['mao_de_obra']['message'];
                $this->logProcessamentoArquivo('Mão de Obra', 'CONCLUIDO', $resultados['mao_de_obra']);
                
                // Passo 2: Gravar Composições
                $this->logProcessamentoArquivo('Composições', 'INICIANDO');
                $resultados['composicoes'] = $this->gravarComposicoes($diretorioProcessamento);
                $detalhesProcessamento[] = "Composições: " . $resultados['composicoes']['message'];
                $this->logProcessamentoArquivo('Composições', 'CONCLUIDO', $resultados['composicoes']);
                
                // Passo 3: Gravar Insumos
                $this->logProcessamentoArquivo('Insumos', 'INICIANDO');
                $resultados['insumos'] = $this->gravarInsumos($diretorioProcessamento);
                $detalhesProcessamento[] = "Insumos: " . $resultados['insumos']['message'];
                $this->logProcessamentoArquivo('Insumos', 'CONCLUIDO', $resultados['insumos']);
                
                // Passo 4: Gravar Analítico
                $this->logProcessamentoArquivo('Analítico', 'INICIANDO');
                try {
                    $datasDoLote = $this->getDatasDoLote($diretorioProcessamento);
                    $resultados['analitico'] = $this->gravarAnalitico($diretorioProcessamento, $datasDoLote['data_base'], $datasDoLote['data_emissao']);
                } catch (\Exception $e) {
                    $this->logSINAPI('warning', 'ANALITICO', 'Erro ao obter datas do lote para analítico, usando datas atuais', [
                        'erro' => $e->getMessage()
                    ]);
                    $resultados['analitico'] = $this->gravarAnalitico($diretorioProcessamento, Carbon::now()->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
                }
                $detalhesProcessamento[] = "Analítico: " . $resultados['analitico']['message'];
                $this->logProcessamentoArquivo('Analítico', 'CONCLUIDO', $resultados['analitico']);
                
                // Passo 5: Atualizar view materializada
                $this->logProcessamentoArquivo('View Materializada', 'INICIANDO');
                $this->atualizarViewComposicoes();
                $detalhesProcessamento[] = "View materializada atualizada";
                $this->logProcessamentoArquivo('View Materializada', 'CONCLUIDO');
                
                DB::commit();
                $this->logSINAPI('info', 'TRANSACAO', 'Transação de gravação SINAPI commitada com sucesso');
                
                $tempoProcessamento = round(microtime(true) - $inicioProcessamento, 2);
                
                // Calcular totais corretamente
                $totalRegistros = 0;
                $totalCriados = 0;
                $totalAtualizados = 0;
                $arquivosProcessados = count($arquivosEncontrados);
                
                foreach ($resultados as $tipo => $resultado) {
                    if (isset($resultado['criados'])) {
                        $totalCriados += $resultado['criados'];
                        $totalRegistros += $resultado['criados'];
                    }
                    if (isset($resultado['atualizados'])) {
                        $totalAtualizados += $resultado['atualizados'];
                        $totalRegistros += $resultado['atualizados'];
                    }
                    if (isset($resultado['total_registros'])) {
                        // Se não temos criados/atualizados, usar total_registros
                        if (!isset($resultado['criados']) && !isset($resultado['atualizados'])) {
                            $totalRegistros += $resultado['total_registros'];
                        }
                    }
                }
                
                $this->logConclusaoGravacao($resultados, $tempoProcessamento);

                // ===================================================================
                // ETAPA 4: RESPOSTA DE SUCESSO
                // ===================================================================
                

                
                return response()->json([
                    'success' => true,
                    'message' => 'Gravação de dados SINAPI concluída com sucesso!',
                    'results' => [
                        'total_registros' => $totalRegistros,
                        'total_criados' => $totalCriados,
                        'total_atualizados' => $totalAtualizados,
                        'arquivos_processados' => $arquivosProcessados,
                        'tempo_processamento' => $tempoProcessamento . 's'
                    ],
                    'details' => $detalhesProcessamento,
                    'resultados_detalhados' => $resultados,
                    'arquivos_processados' => array_column($arquivosEncontrados, 'nome')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            $this->logErroGravacao('Gravação SINAPI', $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'usuario' => auth()->user()->name ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao gravar dados SINAPI no banco: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grava dados de mão de obra no banco de dados
     * 
     * @param string $diretorioProcessamento Diretório com os arquivos processados
     * @return array Estatísticas da operação
     */
    private function gravarMaoDeObra($diretorioProcessamento)
    {

        
        // Obter datas do lote primeiro
        try {
            $datasDoLote = $this->getDatasDoLote($diretorioProcessamento);
        } catch (\Exception $e) {
            $datasDoLote = [
                'data_base' => Carbon::now()->format('Y-m-d'),
                'data_emissao' => Carbon::now()->format('Y-m-d')
            ];
        }
        
        $arquivos = glob($diretorioProcessamento . '/sinapi_mao_obra_*.xlsx');
        
        if (empty($arquivos)) {
            return ['status' => 'aviso', 'message' => 'Nenhum arquivo de mão de obra encontrado.', 'criados' => 0, 'atualizados' => 0, 'erros' => []];
        }
        
        $stats = ['criados' => 0, 'atualizados' => 0, 'erros' => [], 'total_registros' => 0];

        foreach ($arquivos as $arquivo) {
            
            try {
                $spreadsheet = IOFactory::load($arquivo);
                $sheet = $spreadsheet->getSheet(0);
                $rows = $sheet->toArray();
                

                
                array_shift($rows); // Remove cabeçalho
                $linhasProcessadas = 0;
                $linhasComErro = 0;

                foreach ($rows as $index => $row) {
                    try {
                        // Verificar se a linha tem dados válidos
                        if (empty($row[0]) || !isset($row[3])) {
                            continue;
                        }

                        // Determinar tipo de desoneração baseado no nome do arquivo
                        $desoneracao = 'sem';
                        if (strpos($arquivo, 'COM_DESONERACAO') !== false) {
                            $desoneracao = 'com';
                        }

                        // Preparar dados para inserção usando as datas do lote
                        $dados = [
                            'codigo_composicao' => trim($row[0]),
                            'descricao'         => trim($row[1] ?? ''),
                            'unidade'           => trim($row[2] ?? ''),
                            'percentagem_pr'    => (float) ($row[3] ?? 0),
                            'data_emissao'      => $datasDoLote['data_emissao'],
                            'data_base'         => $datasDoLote['data_base'],
                            'desoneracao'       => $desoneracao
                        ];

                        // Verificar se já existe o registro
                        $registroExistente = \App\Models\TabelaOficial\Sinapi\SinapiMaoDeObra::where([
                            'codigo_composicao' => $dados['codigo_composicao'],
                            'data_base'         => $dados['data_base'],
                            'desoneracao'       => $dados['desoneracao']
                        ])->first();

                        if ($registroExistente) {
                            // Atualizar registro existente
                            $registroExistente->update($dados);
                            $stats['atualizados']++;
                        } else {
                            // Criar novo registro
                            \App\Models\TabelaOficial\Sinapi\SinapiMaoDeObra::create($dados);
                            $stats['criados']++;
                        }
                        
                        $linhasProcessadas++;

                    } catch (\Exception $e) {
                        $linhasComErro++;
                        $erroMsg = "Arquivo " . basename($arquivo) . " - Linha " . ($index + 2) . ": " . $e->getMessage();
                        $stats['erros'][] = $erroMsg;
                        Log::error('Erro ao processar linha de mão de obra', [
                            'arquivo' => basename($arquivo),
                            'linha' => $index + 2,
                            'dados' => $row ?? 'vazio',
                            'erro' => $e->getMessage()
                        ]);
                    }
                }
                
                $stats['total_registros'] += $linhasProcessadas;
                

                
            } catch (\Exception $e) {
                $erroMsg = "Erro ao processar arquivo " . basename($arquivo) . ": " . $e->getMessage();
                $stats['erros'][] = $erroMsg;
                Log::error('Erro ao processar arquivo de mão de obra', [
                    'arquivo' => basename($arquivo),
                    'erro' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $message = "Gravação de Mão de Obra concluída. Criados: {$stats['criados']}, Atualizados: {$stats['atualizados']}, Total processados: {$stats['total_registros']}.";
        
        return ['status' => 'sucesso', 'message' => $message] + $stats;
    }

    /**
     * Grava dados de composições no banco de dados
     * 
     * @param string $diretorioProcessamento Diretório com os arquivos processados
     * @return array Estatísticas da operação
     */
    private function gravarComposicoes($diretorioProcessamento)
    {
        Log::info('Iniciando gravação de composições SINAPI', [
            'diretorio' => $diretorioProcessamento
        ]);
        
        $arquivos = glob($diretorioProcessamento . '/sinapi_processado_CCD.xlsx');
        $arquivos = array_merge($arquivos, glob($diretorioProcessamento . '/sinapi_processado_CSD.xlsx'));

        Log::info('Arquivos de composições encontrados', [
            'arquivos' => $arquivos,
            'total_arquivos' => count($arquivos)
        ]);

        if (empty($arquivos)) {
            Log::warning('Nenhum arquivo de composições encontrado', [
                'padrao_busca' => $diretorioProcessamento . '/sinapi_processado_CCD.xlsx e CSD.xlsx'
            ]);
            return ['status' => 'aviso', 'message' => 'Nenhum arquivo de composições encontrado.', 'criados' => 0, 'atualizados' => 0, 'erros' => []];
        }
        
        $stats = ['criados' => 0, 'atualizados' => 0, 'erros' => [], 'total_registros' => 0];

        foreach ($arquivos as $arquivo) {
            Log::info('Processando arquivo de composições', [
                'arquivo' => basename($arquivo),
                'caminho_completo' => $arquivo
            ]);
            
            try {
                $desoneracao = str_contains($arquivo, 'CCD') ? 'com' : 'sem';
                $spreadsheet = IOFactory::load($arquivo);
                $rows = $spreadsheet->getActiveSheet()->toArray();
                
                Log::info('Estrutura do arquivo de composições', [
                    'arquivo' => basename($arquivo),
                    'total_linhas' => count($rows),
                    'desoneracao' => $desoneracao
                ]);
                
                array_shift($rows); // Remove cabeçalho
                $linhasProcessadas = 0;
                $linhasComErro = 0;

                foreach ($rows as $index => $row) {
                    try {
                        if (empty($row[0]) || empty($row[1])) {
                            Log::debug('Linha ignorada por dados insuficientes', [
                                'arquivo' => basename($arquivo),
                                'linha' => $index + 2,
                                'dados' => $row
                            ]);
                            continue;
                        }
                        
                        $dados = [
                            'grupo'             => trim($row[0]),
                            'codigo_composicao' => trim($row[1]),
                            'descricao'         => trim($row[2] ?? ''),
                            'unidade'           => trim($row[3] ?? ''),
                            'custo_pr'          => $this->converterValorMonetario($row[4] ?? 0),
                            'data_base'         => Carbon::createFromFormat('d/m/Y', $row[5])->format('Y-m-d'),
                            'data_emissao'      => Carbon::createFromFormat('d/m/Y', $row[6])->format('Y-m-d'),
                            'desoneracao'       => $desoneracao,
                        ];

                        // Verificar se já existe o registro
                        $registroExistente = DB::table('sinapi_composicoes')->where([
                            'codigo_composicao' => $dados['codigo_composicao'],
                            'data_base' => $dados['data_base'],
                            'desoneracao' => $dados['desoneracao']
                        ])->first();

                        if ($registroExistente) {
                            // Atualizar registro existente
                            DB::table('sinapi_composicoes')->where([
                                'codigo_composicao' => $dados['codigo_composicao'],
                                'data_base' => $dados['data_base'],
                                'desoneracao' => $dados['desoneracao']
                            ])->update($dados);
                            $stats['atualizados']++;
                        } else {
                            // Criar novo registro
                            DB::table('sinapi_composicoes')->insert($dados);
                            $stats['criados']++;
                        }
                        
                        $linhasProcessadas++;

                    } catch (\Exception $e) {
                        $linhasComErro++;
                        $erroMsg = "Arquivo " . basename($arquivo) . " - Linha " . ($index + 2) . ": " . $e->getMessage();
                        $stats['erros'][] = $erroMsg;
                        Log::error('Erro ao processar linha de composições', [
                            'arquivo' => basename($arquivo),
                            'linha' => $index + 2,
                            'dados' => $row ?? 'vazio',
                            'erro' => $e->getMessage()
                        ]);
                    }
                }
                
                $stats['total_registros'] += $linhasProcessadas;
                
                Log::info('Arquivo de composições processado', [
                    'arquivo' => basename($arquivo),
                    'linhas_processadas' => $linhasProcessadas,
                    'linhas_com_erro' => $linhasComErro,
                    'criados_ate_agora' => $stats['criados'],
                    'atualizados_ate_agora' => $stats['atualizados']
                ]);
                
            } catch (\Exception $e) {
                $erroMsg = "Erro ao processar arquivo " . basename($arquivo) . ": " . $e->getMessage();
                $stats['erros'][] = $erroMsg;
                Log::error('Erro ao processar arquivo de composições', [
                    'arquivo' => basename($arquivo),
                    'erro' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $message = "Gravação de Composições concluída. Criados: {$stats['criados']}, Atualizados: {$stats['atualizados']}, Total processados: {$stats['total_registros']}.";
        
        Log::info('Gravação de composições SINAPI concluída', [
            'criados' => $stats['criados'],
            'atualizados' => $stats['atualizados'],
            'total_registros' => $stats['total_registros'],
            'erros' => count($stats['erros']),
            'message' => $message
        ]);
        
        return ['status' => 'sucesso', 'message' => $message] + $stats;
    }

    /**
     * Grava dados de insumos no banco de dados
     * 
     * @param string $diretorioProcessamento Diretório com os arquivos processados
     * @return array Estatísticas da operação
     */
    private function gravarInsumos($diretorioProcessamento)
    {
        Log::info('Iniciando gravação de insumos SINAPI', [
            'diretorio' => $diretorioProcessamento
        ]);
        
        $arquivos = glob($diretorioProcessamento . '/sinapi_processado_ICD.xlsx');
        $arquivos = array_merge($arquivos, glob($diretorioProcessamento . '/sinapi_processado_ISD.xlsx'));

        Log::info('Arquivos de insumos encontrados', [
            'arquivos' => $arquivos,
            'total_arquivos' => count($arquivos)
        ]);

        if (empty($arquivos)) {
            Log::warning('Nenhum arquivo de insumos encontrado', [
                'padrao_busca' => $diretorioProcessamento . '/sinapi_processado_ICD.xlsx e ISD.xlsx'
            ]);
            return ['status' => 'aviso', 'message' => 'Nenhum arquivo de insumos encontrado.', 'criados' => 0, 'atualizados' => 0, 'erros' => []];
        }
        
        $stats = ['criados' => 0, 'atualizados' => 0, 'erros' => [], 'total_registros' => 0];

        foreach ($arquivos as $arquivo) {
            Log::info('Processando arquivo de insumos', [
                'arquivo' => basename($arquivo),
                'caminho_completo' => $arquivo
            ]);
            
            try {
                $desoneracao = str_contains($arquivo, 'ICD') ? 'com' : 'sem';
                $spreadsheet = IOFactory::load($arquivo);
                $rows = $spreadsheet->getActiveSheet()->toArray();
                
                Log::info('Estrutura do arquivo de insumos', [
                    'arquivo' => basename($arquivo),
                    'total_linhas' => count($rows),
                    'desoneracao' => $desoneracao
                ]);
                
                array_shift($rows); // Remove cabeçalho
                $linhasProcessadas = 0;
                $linhasComErro = 0;

                foreach ($rows as $index => $row) {
                    try {
                        if (empty($row[0]) || empty($row[1])) {
                            Log::debug('Linha ignorada por dados insuficientes', [
                                'arquivo' => basename($arquivo),
                                'linha' => $index + 2,
                                'dados' => $row
                            ]);
                            continue;
                        }
                        
                        $dados = [
                            'classificacao' => trim($row[0]),
                            'codigo_insumo' => trim($row[1]),
                            'descricao'     => trim($row[2] ?? ''),
                            'unidade'       => trim($row[3] ?? ''),
                            'custo_pr'      => $this->converterValorMonetario($row[4] ?? 0),
                            'data_base'     => Carbon::createFromFormat('d/m/Y', $row[5])->format('Y-m-d'),
                            'data_emissao'  => Carbon::createFromFormat('d/m/Y', $row[6])->format('Y-m-d'),
                            'desoneracao'   => $desoneracao,
                        ];

                        // Verificar se já existe o registro
                        $registroExistente = \App\Models\TabelaOficial\Sinapi\SinapiInsumo::where([
                            'codigo_insumo' => $dados['codigo_insumo'],
                            'data_base' => $dados['data_base'],
                            'desoneracao' => $dados['desoneracao']
                        ])->first();

                        if ($registroExistente) {
                            // Atualizar registro existente
                            $registroExistente->update($dados);
                            $stats['atualizados']++;
                        } else {
                            // Criar novo registro
                            \App\Models\TabelaOficial\Sinapi\SinapiInsumo::create($dados);
                            $stats['criados']++;
                        }
                        
                        $linhasProcessadas++;

                    } catch (\Exception $e) {
                        $linhasComErro++;
                        $erroMsg = "Arquivo " . basename($arquivo) . " - Linha " . ($index + 2) . ": " . $e->getMessage();
                        $stats['erros'][] = $erroMsg;
                        Log::error('Erro ao processar linha de insumos', [
                            'arquivo' => basename($arquivo),
                            'linha' => $index + 2,
                            'dados' => $row ?? 'vazio',
                            'erro' => $e->getMessage()
                        ]);
                    }
                }
                
                $stats['total_registros'] += $linhasProcessadas;
                
                Log::info('Arquivo de insumos processado', [
                    'arquivo' => basename($arquivo),
                    'linhas_processadas' => $linhasProcessadas,
                    'linhas_com_erro' => $linhasComErro,
                    'criados_ate_agora' => $stats['criados'],
                    'atualizados_ate_agora' => $stats['atualizados']
                ]);
                
            } catch (\Exception $e) {
                $erroMsg = "Erro ao processar arquivo " . basename($arquivo) . ": " . $e->getMessage();
                $stats['erros'][] = $erroMsg;
                Log::error('Erro ao processar arquivo de insumos', [
                    'arquivo' => basename($arquivo),
                    'erro' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $message = "Gravação de Insumos concluída. Criados: {$stats['criados']}, Atualizados: {$stats['atualizados']}, Total processados: {$stats['total_registros']}.";
        
        Log::info('Gravação de insumos SINAPI concluída', [
            'criados' => $stats['criados'],
            'atualizados' => $stats['atualizados'],
            'total_registros' => $stats['total_registros'],
            'erros' => count($stats['erros']),
            'message' => $message
        ]);
        
        return ['status' => 'sucesso', 'message' => $message] + $stats;
    }

    /**
     * Grava dados analíticos no banco de dados
     * 
     * @param string $diretorioProcessamento Diretório com os arquivos processados
     * @param string $data_base Data base do lote
     * @param string $data_emissao Data de emissão do lote
     * @return array Estatísticas da operação
     */
    private function gravarAnalitico($diretorioProcessamento, $data_base, $data_emissao)
    {
        $arquivoAnalitico = $diretorioProcessamento . '/sinapi_processado_Analítico.xlsx';
        
        if (!file_exists($arquivoAnalitico)) {
            return ['status' => 'aviso', 'message' => 'Arquivo analítico não encontrado.', 'composicoes_criadas' => 0, 'composicoes_atualizadas' => 0, 'itens_criados' => 0, 'itens_atualizados' => 0, 'erros' => []];
        }
        
        $stats = ['composicoes_criadas' => 0, 'composicoes_atualizadas' => 0, 'itens_criados' => 0, 'itens_atualizados' => 0, 'erros' => []];
        
        $spreadsheet = IOFactory::load($arquivoAnalitico);
        $rows = $spreadsheet->getActiveSheet()->toArray();
        array_shift($rows);

        $last_codigo_composicao = null;

        foreach ($rows as $index => $row) {
            try {
                if (empty($row[0]) && empty($row[1])) continue;

                $codigo_composicao_atual = trim($row[1]);
                
                if (!empty($codigo_composicao_atual)) {
                    $last_codigo_composicao = $codigo_composicao_atual;
                }
                
                $codigo_composicao = $last_codigo_composicao;

                if (empty($codigo_composicao)) {
                    throw new \Exception("Código da composição não encontrado para o item.");
                }

                $tipoItem = trim($row[2] ?? '');
                $codigoItem = trim($row[3] ?? '');

                // Se não tem código de item, é uma linha de cabeçalho da composição (pai).
                if (empty($tipoItem) && empty($codigoItem)) {
                    $dados = [
                        'grupo'             => trim($row[0]),
                        'codigo_composicao' => $codigo_composicao,
                        'descricao'         => trim($row[4]),
                        'unidade'           => trim($row[5]),
                        'situacao'          => trim($row[7]),
                        'data_base'         => $data_base,
                        'data_emissao'      => $data_emissao
                    ];
                    
                    DB::table('sinapi_composicoes_analitico')->updateOrInsert(
                        [
                            'codigo_composicao' => $dados['codigo_composicao'],
                            'data_base' => $dados['data_base']
                        ],
                        $dados
                    );
                    
                    $stats['composicoes_criadas']++;
                    
                } else { // É um item filho da composição.
                    $dados = [
                        'codigo_composicao' => $codigo_composicao,
                        'tipo_item'         => $tipoItem,
                        'codigo_item'       => $codigoItem,
                        'descricao'         => trim($row[4]),
                        'unidade'           => trim($row[5]),
                        'coeficiente'       => $this->converterValorMonetario($row[6]),
                        'situacao'          => trim($row[7]),
                        'data_base'         => $data_base,
                        'data_emissao'      => $data_emissao
                    ];
                    
                    DB::table('sinapi_itens_analitico')->updateOrInsert(
                        [
                            'codigo_composicao' => $dados['codigo_composicao'],
                            'codigo_item' => $dados['codigo_item'],
                            'data_base' => $dados['data_base']
                        ],
                        $dados
                    );
                    
                    $stats['itens_criados']++;
                }
            } catch (\Exception $e) {
                $stats['erros'][] = "Arquivo " . basename($arquivoAnalitico) . " - Linha " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        $message = "Gravação de Analítico concluída. Comp. Criadas: {$stats['composicoes_criadas']}. Itens Criados: {$stats['itens_criados']}.";
        return ['status' => 'sucesso', 'message' => $message] + $stats;
    }

    /**
     * Extrai a data_base e data_emissao de um arquivo de referência do lote
     * 
     * @param string $diretorioProcessamento Diretório com os arquivos processados
     * @return array Contendo 'data_base' e 'data_emissao'
     */
    private function getDatasDoLote($diretorioProcessamento)
    {
        Log::info('Buscando arquivos de referência para datas do lote', [
            'diretorio' => $diretorioProcessamento
        ]);
        
        $arquivos = glob($diretorioProcessamento . '/sinapi_processado_CSD.xlsx');
        $arquivos = array_merge($arquivos, glob($diretorioProcessamento . '/sinapi_processado_CCD.xlsx'));

        Log::info('Arquivos de referência encontrados', [
            'arquivos' => $arquivos
        ]);

        if (empty($arquivos)) {
            throw new \Exception("Nenhum arquivo de referência (CSD ou CCD) encontrado para determinar a data-base do lote.");
        }

        $arquivoReferencia = $arquivos[0];
        Log::info('Usando arquivo de referência', [
            'arquivo' => basename($arquivoReferencia)
        ]);
        
        try {
            $spreadsheet = IOFactory::load($arquivoReferencia);
            $rows = $spreadsheet->getActiveSheet()->toArray();
            
            Log::info('Estrutura do arquivo de referência', [
                'total_linhas' => count($rows),
                'primeira_linha' => $rows[0] ?? 'vazio',
                'segunda_linha' => $rows[1] ?? 'vazio'
            ]);
            
            $row = $rows[1] ?? null; // Segunda linha (índice 1)

            if (empty($row)) {
                throw new \Exception("Arquivo de referência está vazio: " . basename($arquivoReferencia));
            }

            if (empty($row[5]) || empty($row[6])) {
                Log::error('Colunas de data não encontradas no arquivo de referência', [
                    'arquivo' => basename($arquivoReferencia),
                    'coluna_5' => $row[5] ?? 'vazio',
                    'coluna_6' => $row[6] ?? 'vazio',
                    'todas_colunas' => $row
                ]);
                throw new \Exception("Não foi possível ler a data_base (coluna 6) e data_emissao (coluna 7) do arquivo de referência: " . basename($arquivoReferencia));
            }

            $dataBase = Carbon::createFromFormat('d/m/Y', $row[5])->format('Y-m-d');
            $dataEmissao = Carbon::createFromFormat('d/m/Y', $row[6])->format('Y-m-d');
            
            Log::info('Datas do lote extraídas com sucesso', [
                'data_base' => $dataBase,
                'data_emissao' => $dataEmissao,
                'arquivo_referencia' => basename($arquivoReferencia)
            ]);

            return [
                'data_base' => $dataBase,
                'data_emissao' => $dataEmissao,
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar arquivo de referência para datas', [
                'arquivo' => basename($arquivoReferencia),
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Atualiza a view materializada de composições
     */
    private function atualizarViewComposicoes()
    {
        try {
            // Busca a data base da importação atual
            $dataBase = DB::table('sinapi_composicoes')
                ->select('data_base')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$dataBase) {
                Log::error('Não foi possível identificar a data base da importação atual');
                throw new \Exception('Data base não encontrada');
            }

            Log::info('Atualizando view para data base específica', [
                'data_base' => $dataBase->data_base
            ]);

            // Remove os registros da data base atual
            DB::statement('
                DELETE FROM sinapi_composicoes_view 
                WHERE data_base = ?
            ', [$dataBase->data_base]);

            // Insere os dados da data base atual
            DB::statement('
                INSERT INTO sinapi_composicoes_view (
                    data_base, desoneracao, grupo, codigo, descricao, unidade,
                    valor_mao_obra, valor_mat_equip, valor_total, created_at, updated_at
                )
                SELECT 
                    c.data_base,
                    c.desoneracao,
                    c.grupo,
                    c.codigo_composicao as codigo,
                    c.descricao,
                    c.unidade,
                    IFNULL(c.custo_pr * mo.percentagem_pr, 0) as valor_mao_obra,
                    IFNULL(c.custo_pr - (c.custo_pr * mo.percentagem_pr), c.custo_pr) as valor_mat_equip,
                    c.custo_pr as valor_total,
                    NOW() as created_at,
                    NOW() as updated_at
                FROM sinapi_composicoes c
                LEFT JOIN sinapi_mao_de_obra mo ON 
                    mo.codigo_composicao = c.codigo_composicao AND
                    mo.data_base = c.data_base AND
                    mo.desoneracao = c.desoneracao
                WHERE c.data_base = ?
            ', [$dataBase->data_base]);

            Log::info('View sinapi_composicoes_view atualizada com sucesso', [
                'data_base' => $dataBase->data_base
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar view sinapi_composicoes_view', [
                'erro' => $e->getMessage(),
                'data_base' => $dataBase->data_base ?? null
            ]);
            throw $e;
        }
    }

    /**
     * Converte valor monetário para formato adequado
     * 
     * @param mixed $valor Valor a ser convertido
     * @return float Valor convertido
     */
    private function converterValorMonetario($valor)
    {
        return (float) str_replace(',', '.', $valor);
    }

    /**
     * Verifica arquivos disponíveis para gravação no banco de dados
     * 
     * OBJETIVO: Permitir que o frontend verifique quais arquivos estão disponíveis
     * para gravação antes de iniciar o processo.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificarArquivosDisponiveis()
    {
        $this->verificarPermissao();
        
        try {
            $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Iniciando verificação de arquivos disponíveis');
            
            // Obter diretório de processamento da session
            $nomeDiretorio = session('sinapi_processamento_dir');
            
            if (!$nomeDiretorio) {
                $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Nenhum diretório de processamento encontrado na sessão');
                return response()->json([
                    'success' => true,
                    'message' => 'Nenhum diretório de processamento encontrado',
                    'diretorio' => null,
                    'arquivos_disponiveis' => [],
                    'arquivos_faltantes' => [],
                    'total_esperados' => 0,
                    'total_disponiveis' => 0,
                    'total_faltantes' => 0,
                    'status' => 'sem_diretorio',
                    'pode_gravar' => false
                ]);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            if (!is_dir($diretorioProcessamento)) {
                $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Diretório de processamento não existe no sistema', ['diretorio' => $nomeDiretorio]);
                return response()->json([
                    'success' => true,
                    'message' => 'Diretório de processamento não existe',
                    'diretorio' => $nomeDiretorio,
                    'arquivos_disponiveis' => [],
                    'arquivos_faltantes' => [],
                    'total_esperados' => 0,
                    'total_disponiveis' => 0,
                    'total_faltantes' => 0,
                    'status' => 'diretorio_inexistente',
                    'pode_gravar' => false
                ]);
            }
            
            $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Diretório de processamento validado', ['diretorio' => $nomeDiretorio]);
            
            // Lista de arquivos esperados (7 arquivos no total)
            $arquivosEsperados = [
                // Arquivos da Aba 1 (Composições e Insumos)
                'sinapi_processado_ISD.xlsx',
                'sinapi_processado_ICD.xlsx',
                'sinapi_processado_CSD.xlsx',
                'sinapi_processado_CCD.xlsx',
                'sinapi_processado_Analítico.xlsx',
                // Arquivos da Aba 2 (Mão de Obra)
                'sinapi_mao_obra_SEM_DESONERACAO.xlsx',
                'sinapi_mao_obra_COM_DESONERACAO.xlsx'
            ];
            
            // Verificar existência de cada arquivo
            $arquivosDisponiveis = [];
            $arquivosFaltantes = [];
            
            foreach ($arquivosEsperados as $arquivo) {
                $caminhoArquivo = $diretorioProcessamento . '/' . $arquivo;
                if (file_exists($caminhoArquivo)) {
                    $arquivosDisponiveis[] = [
                        'nome' => $arquivo,
                        'tamanho' => filesize($caminhoArquivo),
                        'data_modificacao' => date('Y-m-d H:i:s', filemtime($caminhoArquivo)),
                        'status' => 'disponivel'
                    ];
                } else {
                    $arquivosFaltantes[] = [
                        'nome' => $arquivo,
                        'status' => 'faltante'
                    ];
                }
            }
            
            // Determinar status geral
            $status = 'completo';
            if (empty($arquivosDisponiveis)) {
                $status = 'sem_arquivos';
            } elseif (!empty($arquivosFaltantes)) {
                $status = 'incompleto';
            }
            
            $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Verificação concluída', [
                'status' => $status,
                'total_disponiveis' => count($arquivosDisponiveis),
                'total_esperados' => count($arquivosEsperados),
                'pode_gravar' => empty($arquivosFaltantes) && !empty($arquivosDisponiveis),
                'diretorio' => $nomeDiretorio
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Verificação de arquivos concluída',
                'diretorio' => $nomeDiretorio,
                'arquivos_disponiveis' => $arquivosDisponiveis,
                'arquivos_faltantes' => $arquivosFaltantes,
                'total_esperados' => count($arquivosEsperados),
                'total_disponiveis' => count($arquivosDisponiveis),
                'total_faltantes' => count($arquivosFaltantes),
                'status' => $status,
                'pode_gravar' => empty($arquivosFaltantes) && !empty($arquivosDisponiveis)
            ]);

        } catch (\Exception $e) {
            $this->logErroCritico('VERIFICACAO_ARQUIVOS', $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar arquivos disponíveis: ' . $e->getMessage(),
                'arquivos_disponiveis' => [],
                'arquivos_esperados' => [],
                'status' => 'erro'
            ], 500);
        }
    }


    // ========================================
    // MÉTODOS DE LOG ESPECÍFICOS PARA SINAPI
    // ========================================

    /**
     * Loga uma mensagem no arquivo de log específico da importação SINAPI
     * 
     * @param string $level Nível do log (info, error, warning, debug)
     * @param string $origin Origem/Módulo da mensagem
     * @param string $message Mensagem descritiva do evento
     * @param array $context Dados adicionais para o log
     */
    private function logSINAPI($level, $origin, $message, $context = [])
    {
        $user = auth()->user();
        $userInfo = $user ? "Usuario: {$user->id} ({$user->name})" : "Usuario: N/A";
        $ip = request()->ip();

        $formattedMessage = "[{$origin}] [{$userInfo}] [IP: {$ip}] - {$message}";

        // Cria um logger on-the-fly que aponta para o arquivo específico SINAPI
        $logger = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/importacao_sinapi.log'),
        ]);

        $logger->{$level}($formattedMessage, $context);
    }

    /**
     * Loga o início de uma operação de processamento
     * 
     * @param string $operation Nome da operação
     * @param array $context Dados adicionais
     */
    private function logInicioProcessamento($operation, $context = [])
    {
        $this->logSINAPI('info', 'PROCESSAMENTO', "Início da operação: {$operation}", $context);
    }

    /**
     * Loga o sucesso de uma operação de processamento
     * 
     * @param string $operation Nome da operação
     * @param array $resultados Resultados da operação
     */
    private function logSucessoProcessamento($operation, $resultados = [])
    {
        $this->logSINAPI('info', 'PROCESSAMENTO', "Operação concluída com sucesso: {$operation}", $resultados);
    }

    /**
     * Loga erro em uma operação de processamento
     * 
     * @param string $operation Nome da operação
     * @param string $error Mensagem de erro
     * @param array $context Contexto adicional
     */
    private function logErroProcessamento($operation, $error, $context = [])
    {
        $this->logSINAPI('error', 'PROCESSAMENTO', "Erro na operação {$operation}: {$error}", $context);
    }

    /**
     * Loga o início da operação de gravação no banco
     * 
     * @param array $arquivos Arquivos a serem gravados
     * @param Request $request Requisição HTTP
     */
    private function logInicioGravacao($arquivos, Request $request)
    {
        $user = auth()->user();
        $usuarioInfo = $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ] : ['id' => null, 'name' => 'guest', 'email' => null];
        
        $ip = $request->ip();
        $arquivosNomes = collect($arquivos)->map(fn($f) => $f['nome'] ?? 'arquivo_desconhecido')->toArray();
        
        $this->logSINAPI('info', 'GRAVACAO_BANCO', 'INICIO', [
            'usuario' => $usuarioInfo,
            'ip' => $ip,
            'arquivos' => $arquivosNomes
        ]);
    }

    /**
     * Loga o processamento de um arquivo específico
     * 
     * @param string $nomeArquivo Nome do arquivo
     * @param string $status Status do processamento
     * @param array $detalhes Detalhes adicionais
     */
    private function logProcessamentoArquivo($nomeArquivo, $status, $detalhes = [])
    {
        $this->logSINAPI('info', 'GRAVACAO_BANCO', "PROCESSANDO | Arquivo: {$nomeArquivo} | Status: {$status}", $detalhes);
    }

    /**
     * Loga a conclusão da operação de gravação
     * 
     * @param array $resultados Resultados da gravação
     * @param float $tempoProcessamento Tempo de processamento
     */
    private function logConclusaoGravacao($resultados, $tempoProcessamento)
    {
        $this->logSINAPI('info', 'GRAVACAO_BANCO', "CONCLUIDO | Tempo: {$tempoProcessamento}s", $resultados);
    }

    /**
     * Loga erros durante a gravação
     * 
     * @param string $arquivo Nome do arquivo
     * @param string $erro Mensagem de erro
     * @param array $contexto Contexto adicional
     */
    private function logErroGravacao($arquivo, $erro, $contexto = [])
    {
        $this->logSINAPI('error', 'GRAVACAO_BANCO', "ERRO | Arquivo: {$arquivo} | Erro: {$erro}", $contexto);
    }

    /**
     * Remove um diretório e todo seu conteúdo recursivamente
     * 
     * @param string $dir Caminho do diretório a ser removido
     * @return bool true se removido com sucesso, false caso contrário
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

    // ========================================
    // SISTEMA DE LOGS CENTRALIZADO PADRONIZADO
    // ========================================

    /**
     * Log centralizado para importação SINAPI
     * 
     * @param string $level Nível do log (info, error, warning)
     * @param string $origin Origem da operação
     * @param string $message Mensagem descritiva
     * @param array $context Contexto adicional
     */
    private function logImportacaoSINAPI($level, $origin, $message, $context = [])
    {
        // Capturar usuário autenticado corretamente
        $user = auth()->user();
        
        // Verificar se o usuário está autenticado
        if ($user) {
            $userInfo = "Usuario: {$user->id} ({$user->name})";
        } else {
            // Se não estiver autenticado, tentar capturar da sessão ou request
            $userInfo = "Usuario: N/A (Não autenticado)";
        }
        
        $ip = request()->ip();
        $timestamp = now()->format('Y-m-d H:i:s');

        $formattedMessage = "[{$timestamp}] [SINAPI] [{$origin}] [{$userInfo}] [IP: {$ip}] - {$message}";

        // Grava no log centralizado de importação de tabelas oficiais
        $logPath = storage_path('logs/importacao_tabelas_oficiais.log');
        $logDir = dirname($logPath);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logEntry = $formattedMessage;
        if (!empty($context)) {
            $logEntry .= " | Contexto: " . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        $logEntry .= "\n";

        file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log de início de operação SINAPI
     */
    private function logInicioOperacao($operation, $context = [])
    {
        $this->logImportacaoSINAPI('info', 'INICIO_OPERACAO', "Iniciando: {$operation}", $context);
    }

    /**
     * Log de progresso da operação SINAPI
     */
    private function logProgresso($operation, $status, $context = [])
    {
        $this->logImportacaoSINAPI('info', 'PROGRESSO', "{$operation}: {$status}", $context);
    }

    /**
     * Log de sucesso da operação SINAPI
     */
    private function logSucesso($operation, $resultados = [], $context = [])
    {
        $this->logImportacaoSINAPI('info', 'SUCESSO', "Concluído com sucesso: {$operation}", array_merge($resultados, $context));
    }

    /**
     * Log de erro de validação/dados SINAPI (não crítico)
     */
    private function logErroValidacao($operation, $error, $context = [])
    {
        $this->logImportacaoSINAPI('error', 'VALIDACAO', "Erro de validação em {$operation}: {$error}", $context);
    }

    /**
     * Log de erro crítico do sistema SINAPI
     */
    private function logErroCritico($operation, $error, $context = [])
    {
        // Erro crítico vai para ambos os logs
        $this->logImportacaoSINAPI('error', 'ERRO_CRITICO', "Erro crítico em {$operation}: {$error}", $context);
        
        // E também para o Laravel.log (erro crítico do sistema)
        Log::error("Erro crítico na importação SINAPI: {$operation} - {$error}", $context);
    }

    /**
     * Método de teste para verificar o sistema de logs
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testarLogs()
    {
        $this->verificarPermissao();
        
        try {
            // Teste 1: Verificar se o usuário está autenticado
            $user = auth()->user();
            $authCheck = auth()->check();
            
            // Teste 2: Verificar informações da sessão
            $sessionId = session()->getId();
            
            // Teste 3: Verificar se há token JWT
            $request = request();
            $authorization = $request->header('Authorization');
            $bearerToken = $request->bearerToken();
            
            // Teste 4: Verificar cookies
            $cookies = $request->cookies->all();
            
            // Log de teste
            $this->logInicioOperacao('TESTE_LOGS', [
                'usuario_id' => $user ? $user->id : 'N/A',
                'usuario_nome' => $user ? $user->name : 'N/A',
                'autenticado' => $authCheck ? 'Sim' : 'Não',
                'session_id' => $sessionId,
                'tem_authorization' => $authorization ? 'Sim' : 'Não',
                'tem_bearer_token' => $bearerToken ? 'Sim' : 'Não'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Teste de logs SINAPI executado',
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
