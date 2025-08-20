<?php

namespace App\Http\Controllers\Api\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
    DerprTransporte
};

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
    public function __construct()
    {
        $this->middleware('auth');
    }

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
            
            $this->logInicioOperacao('PROCESSAMENTO_SERVICOS_GERAIS', [
                'arquivo' => $request->file('arquivo')->getClientOriginalName(),
                'tamanho' => $request->file('arquivo')->getSize()
            ]);
            
            // Validar arquivo enviado
            $validacao = $this->validarArquivoPDF($request);
            if (!$validacao['sucesso']) {
                $this->logErroValidacao('VALIDACAO_PDF', $validacao['mensagem'], [
                    'arquivo' => $request->file('arquivo')->getClientOriginalName()
                ]);
                return response()->json(['message' => $validacao['mensagem']], 400);
            }
            
            $this->logProgresso('VALIDACAO_PDF', 'Arquivo PDF validado com sucesso');
            
            // ===================================================================
            // ETAPA 2: LIMPEZA DE DIRETÓRIOS ANTERIORES
            // ===================================================================
            
            $this->logProgresso('LIMPEZA_DIRETORIOS', 'Iniciando limpeza de diretórios anteriores');
            
            // Verificar e apagar diretórios anteriores de processamento DERPR
            $tempDir = storage_path('app/temp');
            if (is_dir($tempDir)) {
                $subdirs = glob($tempDir . '/processado_derpr_*', GLOB_ONLYDIR);
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
            
            $this->logProgresso('DIRETORIO_CRIADO', 'Diretório de processamento criado', ['diretorio' => $nomeSaidaDir]);
            
            // ===================================================================
            // ETAPA 4: EXECUÇÃO DO SCRIPT PYTHON
            // ===================================================================
            
            $this->logProgresso('SCRIPT_PYTHON', 'Executando script Python para extração de dados');
            
            // Executar script Python
            $dados = $this->executarScriptPython($caminhoCompleto, 'servicos');
            
            $this->logProgresso('SCRIPT_PYTHON', 'Script Python executado com sucesso', [
                'total_composicoes' => count($dados)
            ]);
            
            // Limpar arquivo temporário
            Storage::delete($caminhoCompleto);
            $this->logProgresso('LIMPEZA', 'Arquivo temporário removido');
            
            $this->logSucesso('PROCESSAMENTO_SERVICOS_GERAIS', [
                'total_composicoes_extraidas' => count($dados),
                'diretorio_processamento' => $nomeSaidaDir
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $dados,
                'message' => 'Arquivo processado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            $this->logErroCritico('PROCESSAMENTO_SERVICOS_GERAIS', $e->getMessage(), [
                'arquivo' => $request->file('arquivo')->getClientOriginalName() ?? 'N/A'
            ]);
            
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
            
            $this->logInicioOperacao('PROCESSAMENTO_INSUMOS', [
                'arquivo' => $request->file('arquivo')->getClientOriginalName(),
                'tamanho' => $request->file('arquivo')->getSize()
            ]);
            
            // Validar arquivo enviado
            $validacao = $this->validarArquivoPDF($request);
            if (!$validacao['sucesso']) {
                $this->logErroValidacao('VALIDACAO_PDF', $validacao['mensagem'], [
                    'arquivo' => $request->file('arquivo')->getClientOriginalName()
                ]);
                return response()->json(['message' => $validacao['mensagem']], 400);
            }
            
            $this->logProgresso('VALIDACAO_PDF', 'Arquivo PDF validado com sucesso');
            
            // ===================================================================
            // ETAPA 2: VERIFICAR SE EXISTE DIRETÓRIO DA SESSÃO
            // ===================================================================
            
            $this->logProgresso('VERIFICACAO_SESSAO', 'Verificando diretório de processamento da sessão');
            
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('VERIFICACAO_SESSAO', 'Diretório de processamento não encontrado na sessão');
                return response()->json([
                    'message' => 'É necessário processar primeiro a Aba 1 (Serviços Gerais)'
                ], 400);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            if (!file_exists($diretorioProcessamento)) {
                $this->logErroValidacao('VERIFICACAO_SESSAO', 'Diretório de processamento não existe no sistema');
                return response()->json([
                    'message' => 'Diretório de processamento não encontrado. Processe primeiro a Aba 1.'
                ], 400);
            }
            
            $this->logProgresso('VERIFICACAO_SESSAO', 'Diretório de processamento validado', ['diretorio' => $nomeDiretorio]);
            
            // ===================================================================
            // ETAPA 3: SALVAMENTO TEMPORÁRIO
            // ===================================================================
            
            $this->logProgresso('ARQUIVO_TEMPORARIO', 'Salvando arquivo temporariamente');
            
            $arquivo = $request->file('arquivo');
            $caminhoCompleto = $this->salvarArquivoTemporario($arquivo);
            
            // ===================================================================
            // ETAPA 4: EXECUÇÃO DO SCRIPT PYTHON
            // ===================================================================
            
            $this->logProgresso('SCRIPT_PYTHON', 'Executando script Python para extração de dados');
            
            // Executar script Python
            $this->logProgresso('DEBUG_PYTHON', 'Chamando script Python para insumos', [
                'arquivo' => $request->file('arquivo')->getClientOriginalName(),
                'caminho_temp' => $caminhoCompleto,
                'tipo' => 'insumos'
            ]);
            
            $dados = $this->executarScriptPython($caminhoCompleto, 'insumos');
            
            // Log detalhado dos dados retornados
            $this->logProgresso('DEBUG_DADOS_RETORNADOS', 'Dados retornados pelo Python', [
                'tipo_dados' => gettype($dados),
                'estrutura' => is_array($dados) ? array_keys($dados) : 'não é array',
                'dados_completos' => $dados
            ]);
            
            $this->logProgresso('SCRIPT_PYTHON', 'Script Python executado com sucesso', [
                'total_equipamentos' => count($dados['equipamentos'] ?? []),
                'total_mao_de_obra' => count($dados['mao_de_obra'] ?? []),
                'total_materiais' => count($dados['materiais'] ?? []),
                'total_servicos' => count($dados['servicos'] ?? []),
                'total_transportes' => count($dados['transportes'] ?? [])
            ]);
            
            // Limpar arquivo temporário
            Storage::delete($caminhoCompleto);
            $this->logProgresso('LIMPEZA', 'Arquivo temporário removido');
            
            $this->logSucesso('PROCESSAMENTO_INSUMOS', [
                'total_insumos_extraidos' => array_sum([
                    count($dados['equipamentos'] ?? []),
                    count($dados['mao_de_obra'] ?? []),
                    count($dados['materiais'] ?? []),
                    count($dados['servicos'] ?? []),
                    count($dados['transportes'] ?? [])
                ]),
                'diretorio_processamento' => $nomeDiretorio
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $dados,
                'message' => 'Arquivo processado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            $this->logErroCritico('PROCESSAMENTO_INSUMOS', $e->getMessage(), [
                'arquivo' => $request->file('arquivo')->getClientOriginalName() ?? 'N/A'
            ]);
            
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
            
            $this->logInicioOperacao('PROCESSAMENTO_FORMULAS_TRANSPORTE', [
                'arquivo' => $request->file('arquivo')->getClientOriginalName(),
                'tamanho' => $request->file('arquivo')->getSize()
            ]);
            
            // Validar arquivo enviado
            $validacao = $this->validarArquivoPDF($request);
            if (!$validacao['sucesso']) {
                $this->logErroValidacao('VALIDACAO_PDF', $validacao['mensagem'], [
                    'arquivo' => $request->file('arquivo')->getClientOriginalName()
                ]);
                return response()->json(['message' => $validacao['mensagem']], 400);
            }
            
            $this->logProgresso('VALIDACAO_PDF', 'Arquivo PDF validado com sucesso');
            
            // ===================================================================
            // ETAPA 2: VERIFICAR SE EXISTE DIRETÓRIO DA SESSÃO
            // ===================================================================
            
            $this->logProgresso('VERIFICACAO_SESSAO', 'Verificando diretório de processamento da sessão');
            
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('VERIFICACAO_SESSAO', 'Diretório de processamento não encontrado na sessão');
                return response()->json([
                    'message' => 'É necessário processar primeiro a Aba 1 (Serviços Gerais)'
                ], 400);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            if (!file_exists($diretorioProcessamento)) {
                $this->logErroValidacao('VERIFICACAO_SESSAO', 'Diretório de processamento não existe no sistema');
                return response()->json([
                    'message' => 'Diretório de processamento não encontrado. Processe primeiro a Aba 1.'
                ], 400);
            }
            
            $this->logProgresso('VERIFICACAO_SESSAO', 'Diretório de processamento validado', ['diretorio' => $nomeDiretorio]);
            
            // ===================================================================
            // ETAPA 3: SALVAMENTO TEMPORÁRIO
            // ===================================================================
            
            $this->logProgresso('ARQUIVO_TEMPORARIO', 'Salvando arquivo temporariamente');
            
            $arquivo = $request->file('arquivo');
            $caminhoCompleto = $this->salvarArquivoTemporario($arquivo);
            
            // ===================================================================
            // ETAPA 4: EXECUÇÃO DO SCRIPT PYTHON
            // ===================================================================
            
            $this->logProgresso('SCRIPT_PYTHON', 'Executando script Python para extração de dados');
            
            // Executar script Python
            $dados = $this->executarScriptPython($caminhoCompleto, 'formulas_transporte');
            
            $this->logProgresso('SCRIPT_PYTHON', 'Script Python executado com sucesso', [
                'total_formulas' => isset($dados['data']) ? count($dados['data']) : count($dados)
            ]);
            
            // Limpar arquivo temporário
            Storage::delete($caminhoCompleto);
            $this->logProgresso('LIMPEZA', 'Arquivo temporário removido');
            
            $this->logSucesso('PROCESSAMENTO_FORMULAS_TRANSPORTE', [
                'total_formulas_extraidas' => isset($dados['data']) ? count($dados['data']) : count($dados),
                'diretorio_processamento' => session('derpr_processamento_dir')
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $dados,
                'message' => 'Arquivo processado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            $this->logErroCritico('PROCESSAMENTO_FORMULAS_TRANSPORTE', $e->getMessage(), [
                'arquivo' => $request->file('arquivo')->getClientOriginalName() ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            
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

        // Log detalhado da saída do Python
        $this->logProgresso('DEBUG_PYTHON_SAIDA', 'Saída do script Python', [
            'tipo' => $tipo,
            'saida_bruta' => $saida,
            'tamanho_saida' => strlen($saida),
            'primeiros_100_chars' => substr($saida, 0, 100)
        ]);

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
            $this->logProgresso('EXCEL_COMPOSICOES', 'Iniciando salvamento do arquivo Excel');
            
            // Obter diretório da sessão ou criar novo se não existir
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('EXCEL_COMPOSICOES', 'Diretório de processamento não encontrado na sessão');
                throw new \Exception('Diretório de processamento não encontrado na sessão');
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            // Criar diretório se não existir
            if (!file_exists($diretorioProcessamento)) {
                mkdir($diretorioProcessamento, 0755, true);
                $this->logProgresso('EXCEL_COMPOSICOES', 'Diretório criado com sucesso');
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

            $this->logSucesso('EXCEL_COMPOSICOES', [
                'arquivo_excel' => $nomeArquivo,
                'arquivo_metadata' => 'derpr_metadata.json',
                'total_registros' => count($dados),
                'diretorio' => $nomeDiretorio
            ]);

        } catch (\Exception $e) {
            $this->logErroCritico('EXCEL_COMPOSICOES', $e->getMessage(), [
                'arquivo' => $nomeArquivo,
                'diretorio' => $nomeDiretorio ?? 'N/A'
            ]);
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
            $this->logProgresso('EXCEL_INSUMOS', 'Iniciando salvamento dos arquivos Excel de insumos');
            
            // Obter diretório da sessão
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('EXCEL_INSUMOS', 'Diretório de processamento não encontrado na sessão');
                throw new \Exception('Diretório de processamento não encontrado na sessão');
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            // Criar diretório se não existir
            if (!file_exists($diretorioProcessamento)) {
                mkdir($diretorioProcessamento, 0755, true);
                $this->logProgresso('EXCEL_INSUMOS', 'Diretório criado com sucesso');
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
                    $this->logProgresso('EXCEL_INSUMOS', "Processando arquivo {$tipo}", ['total_itens' => count($dados[$tipo])]);
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

            $this->logSucesso('EXCEL_INSUMOS', [
                'arquivos_processados' => $arquivosProcessados,
                'arquivo_metadata' => 'derpr_metadata_insumos.json',
                'total_arquivos' => count($arquivosProcessados),
                'diretorio' => $nomeDiretorio
            ]);

        } catch (\Exception $e) {
            $this->logErroCritico('EXCEL_INSUMOS', $e->getMessage(), [
                'diretorio' => $nomeDiretorio ?? 'N/A'
            ]);
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
        
        // Mapeamento dos arquivos gerados para os esperados pela configuração
        $mapeamentoArquivos = [
            'derpr_composicoes.xlsx' => 'composicoes.xlsx',
            'derpr_equipamentos.xlsx' => 'equipamentos.xlsx',
            'derpr_mao_de_obra.xlsx' => 'mao_de_obra.xlsx',
            'derpr_itens_incidencia.xlsx' => 'itens_incidencia.xlsx',
            'derpr_materiais.xlsx' => 'materiais.xlsx',
            'derpr_servicos.xlsx' => 'servicos.xlsx',
            'derpr_transportes.xlsx' => 'transportes.xlsx',
            'derpr_formulas_transporte.xlsx' => 'formulas_transporte.xlsx'
        ];
        
        foreach ($mapeamentoArquivos as $arquivoGerado => $arquivoEsperado) {
            $caminhoArquivo = $diretorioProcessamento . '/' . $arquivoGerado;
            
            if (file_exists($caminhoArquivo)) {
                $config = $configuracao[$arquivoEsperado];
                $resultado = $this->processarESalvarArquivo($caminhoArquivo, $arquivoEsperado, $config);
                $resultados[$arquivoEsperado] = $resultado;
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
            $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Iniciando verificação de arquivos disponíveis');
            
            $nomeDiretorio = session('derpr_processamento_dir');
            
            if (!$nomeDiretorio) {
                $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Nenhum diretório de processamento encontrado na sessão');
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
                        'derpr_formulas_transporte.xlsx'
                    ],
                    'diretorio' => null
                ]);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            if (!file_exists($diretorioProcessamento)) {
                $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Diretório de processamento não existe no sistema', ['diretorio' => $nomeDiretorio]);
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
                        'derpr_formulas_transporte.xlsx'
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
                'derpr_formulas_transporte.xlsx'
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
            
            $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Verificação concluída', [
                'status' => $status,
                'total_disponiveis' => $totalDisponiveis,
                'total_esperados' => count($arquivosEsperados),
                'pode_gravar' => $podeGravar,
                'diretorio' => $nomeDiretorio
            ]);
            
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
            $this->logErroCritico('VERIFICACAO_ARQUIVOS', $e->getMessage());
            
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

    /**
     * Log do início da gravação automática
     * 
     * @param Request $request
     * @return void
     */
    private function logarInicioGravacaoAutomatica(Request $request): void
    {
        $diretorio = session('derpr_processamento_dir');
        
        $this->logInicioOperacao('GRAVACAO_AUTOMATICA', [
            'diretorio_processamento' => $diretorio
        ]);
    }

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
            $this->logInicioOperacao('IMPORTACAO_LOTE', [
                'usuario' => auth()->user() ? auth()->user()->name : 'N/A',
                'ip' => request()->ip()
            ]);
            
            // ===================================================================
            // ETAPA 1: VERIFICAR SE EXISTE DIRETÓRIO DA SESSÃO
            // ===================================================================
            
            $this->logProgresso('VERIFICACAO_SESSAO', 'Verificando diretório de processamento da sessão');
            
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('VERIFICACAO_SESSAO', 'Diretório de processamento não encontrado na sessão');
                return response()->json([
                    'success' => false,
                    'message' => 'É necessário processar primeiro as Abas 1, 2 e 3'
                ], 400);
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            if (!file_exists($diretorioProcessamento)) {
                $this->logErroValidacao('VERIFICACAO_SESSAO', 'Diretório de processamento não existe no sistema');
                return response()->json([
                    'success' => false,
                    'message' => 'Diretório de processamento não encontrado. Processe primeiro as Abas 1, 2, 3 e 4.'
                ], 400);
            }
            
            $this->logProgresso('VERIFICACAO_SESSAO', 'Diretório de processamento validado', ['diretorio' => $nomeDiretorio]);
            
            // ===================================================================
            // ETAPA 2: VERIFICAR SE EXISTEM OS ARQUIVOS NECESSÁRIOS
            // ===================================================================
            
            $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Verificando arquivos necessários para importação');
            
            $arquivosEsperados = [
                'derpr_composicoes.xlsx',
                'derpr_equipamentos.xlsx',
                'derpr_mao_de_obra.xlsx',
                'derpr_itens_incidencia.xlsx',
                'derpr_materiais.xlsx',
                'derpr_servicos.xlsx',
                'derpr_transportes.xlsx',
                'derpr_formulas_transporte.xlsx'
            ];
            
            $arquivosFaltantes = [];
            foreach ($arquivosEsperados as $arquivo) {
                if (!file_exists($diretorioProcessamento . '/' . $arquivo)) {
                    $arquivosFaltantes[] = $arquivo;
                }
            }
            
            if (!empty($arquivosFaltantes)) {
                $this->logErroValidacao('VERIFICACAO_ARQUIVOS', 'Arquivos faltando para importação', ['arquivos_faltantes' => $arquivosFaltantes]);
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivos faltando: ' . implode(', ', $arquivosFaltantes) . '. Processe primeiro as Abas 1, 2 e 3.'
                ], 400);
            }
            
            $this->logProgresso('VERIFICACAO_ARQUIVOS', 'Todos os arquivos necessários estão disponíveis');
            
            // ===================================================================
            // ETAPA 3: PROCESSAR ARQUIVOS AUTOMATICAMENTE
            // ===================================================================
            
            $this->logProgresso('PROCESSAMENTO_LOTE', 'Iniciando processamento automático dos arquivos');
            
            $configuracao = $this->getConfiguracaoArquivos();
            
            // Log do início da gravação no banco
            $this->logarInicioGravacaoAutomatica($request);
            
            try {
                // Processar e salvar dados
                $resultados = $this->processarArquivosAutomaticos($diretorioProcessamento, $configuracao);
                
                // Calcular tempo de processamento
                $tempoProcessamento = microtime(true) - $tempoInicio;
                
                // Log da conclusão da gravação
                $this->logarConclusaoGravacao($resultados, $tempoProcessamento);
                
                // Gerar mensagem de resumo
                $mensagem = $this->gerarMensagemResumo($resultados);
                
                // Atualizar view de composições
                $this->atualizarViewComposicoes();
                
                $this->logSucesso('IMPORTACAO_LOTE', [
                    'tempo_processamento' => $tempoProcessamento,
                    'total_arquivos_processados' => count($resultados),
                    'diretorio' => $nomeDiretorio
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $mensagem,
                    'resultados' => $resultados
                ], 200);
                
            } catch (\Exception $e) {
                $tempoProcessamento = microtime(true) - $tempoInicio;
                
                // Log do erro
                $this->logarErroGravacao('ERRO_GERAL', $e->getMessage(), [
                    'tempo_processamento' => $tempoProcessamento,
                    'diretorio' => $nomeDiretorio
                ]);
                
                throw $e;
            }
            
        } catch (\Exception $e) {
            $this->logErroCritico('IMPORTACAO_LOTE', $e->getMessage());
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

    /**
     * Loga os arquivos recebidos para importação
     * 
     * @param array $arquivos
     */
    private function logarArquivosRecebidos($arquivos): void
    {
        $logPath = storage_path('logs/importacao_tabelas_oficiais.log');
        $logMsg = "[" . now()->toDateTimeString() . "] [LOTE_DERPR] Arquivos recebidos:\n";
        foreach ($arquivos as $arquivo) {
            $logMsg .= sprintf("- %s (tamanho: %s bytes)\n", 
                $arquivo->getClientOriginalName(),
                $arquivo->getSize()
            );
        }
        file_put_contents($logPath, $logMsg, FILE_APPEND);
    }

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

    /**
     * Loga o resumo do processamento
     * 
     * @param string $mensagem
     */
    private function logarResumoProcessamento($mensagem): void
    {
        $logPath = storage_path('logs/importacao_tabelas_oficiais.log');
        $logMsg = "[" . now()->toDateTimeString() . "] [LOTE_DERPR] Resumo do processamento:\n";
        $logMsg .= $mensagem . "\n";
        file_put_contents($logPath, $logMsg, FILE_APPEND);
    }

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

    // ========================================
    // MÉTODOS DE LOG PARA GRAVAÇÃO NO BANCO
    // ========================================

    /**
     * Loga o início da operação de gravação no banco
     * 
     * @param array $arquivos
     * @param Request $request
     */
    private function logarInicioGravacao($arquivos, Request $request): void
    {
        $arquivosNomes = collect($arquivos)->map(fn($f) => $f->getClientOriginalName())->toArray();
        
        $this->logInicioOperacao('GRAVACAO_BANCO', [
            'arquivos' => $arquivosNomes
        ]);
    }

    /**
     * Loga o processamento de um arquivo específico
     * 
     * @param string $nomeArquivo
     * @param string $status
     * @param array $detalhes
     */
    private function logarProcessamentoArquivo(string $nomeArquivo, string $status, array $detalhes = []): void
    {
        $this->logProgresso('GRAVACAO_BANCO', "Processando arquivo: {$nomeArquivo} - Status: {$status}", $detalhes);
    }

    /**
     * Loga a conclusão da operação de gravação
     * 
     * @param array $resultados
     * @param float $tempoProcessamento
     */
    private function logarConclusaoGravacao(array $resultados, float $tempoProcessamento): void
    {
        // Preparar resumo dos resultados
        $resumo = [];
        foreach ($resultados as $arquivo => $resultado) {
            $resumo[$arquivo] = [
                'inseridos' => $resultado['registros_inseridos'] ?? 0,
                'atualizados' => $resultado['registros_atualizados'] ?? 0,
                'total' => $resultado['total_registros'] ?? 0
            ];
        }
        
        $this->logSucesso('GRAVACAO_BANCO', [
            'tempo_processamento' => $tempoProcessamento,
            'resultados' => $resumo
        ]);
    }

    /**
     * Loga erros durante a gravação
     * 
     * @param string $arquivo
     * @param string $erro
     * @param array $contexto
     */
    private function logarErroGravacao(string $arquivo, string $erro, array $contexto = []): void
    {
        $this->logErroCritico('GRAVACAO_BANCO', $erro, array_merge([
            'arquivo' => $arquivo
        ], $contexto));
    }

    /**
     * Configuração dos arquivos esperados e suas colunas obrigatórias
     */
    private function getConfiguracaoArquivos()
    {
        return [
            'composicoes.xlsx' => [
                'colunas_obrigatorias' => [
                    'grupo', 'data_base', 'honerado', 'codigo', 'descricao', 
                    'unidade', 'custo_execucao', 'custo_material', 
                    'custo_sub_servico', 'custo_unitario', 'transporte'
                ],
                'model' => DerprComposicao::class
            ],
            'equipamentos.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo_equipamento', 
                    'quantidade', 'ut_produtiva', 'ut_improdutiva', 
                    'vl_hr_prod', 'vl_hr_imp', 'custo_horario'
                ],
                'model' => DerprEquipamento::class
            ],
            'mao_de_obra.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 
                    'eq_salarial', 'encargos_percentagem', 'sal_hora', 
                    'consumo', 'custo_horario'
                ],
                'model' => DerprMaoDeObra::class
            ],
            'itens_incidencia.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 
                    'percentagem', 'tem_mo', 'custo'
                ],
                'model' => DerprItemIncidencia::class
            ],
            'materiais.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 'unid', 
                    'custo_unitario', 'consumo', 'custo_unitario_final'
                ],
                'model' => DerprMaterial::class
            ],
            'servicos.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 'unid', 
                    'custo_unitario', 'consumo', 'custo_unitario_final'
                ],
                'model' => DerprServico::class
            ],
            'transportes.xlsx' => [
                'colunas_obrigatorias' => [
                    'codigo_servico', 'descricao_servico', 'unidade_servico', 
                    'data_base', 'honerado', 'descricao', 'codigo', 'unid', 
                    'formula1', 'formula2', 'custo', 'consumo', 'custo_unitario'
                ],
                'model' => DerprTransporte::class
            ],
            'formulas_transporte.xlsx' => [
                'colunas_obrigatorias' => [
                    'data_base', 'desoneracao', 'codigo', 'descricao', 
                    'unidade', 'formula_transporte'
                ],
                'model' => \App\Models\Importacao\Derpr\DerprFormulaTransporte::class
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
                throw new \Exception('Colunas faltando: ' . implode(', ', $colunasFaltantes));
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
            
            // Log para debug
            $this->logProgresso('PROCESSAMENTO_ARQUIVO', "Cabeçalhos mapeados para {$nomeArquivo}", ['colunas' => $colunas]);
            
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
                        case 'composicoes.xlsx':
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

                        case 'equipamentos.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $model->data_base = \Carbon\Carbon::createFromFormat('d/m/Y', trim($linha[$colunas['data_base']]))->format('Y-m-d');
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
                        
                                                
                        case 'mao_de_obra.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $model->data_base = \Carbon\Carbon::createFromFormat('d/m/Y', trim($linha[$colunas['data_base']]))->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->eq_salarial = $this->paraDecimal($linha[$colunas['eq_salarial']]);
                            $model->encargos_percentagem = $this->paraDecimal($linha[$colunas['encargos_percentagem']]);
                            $model->sal_hora = $this->paraDecimal($linha[$colunas['sal_hora']]);
                            $model->consumo = (float) str_replace(',', '.', $linha[$colunas['consumo']]);
                            $model->custo_horario = $this->paraDecimal($linha[$colunas['custo_horario']]);
                            break;

                        case 'itens_incidencia.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $model->data_base = \Carbon\Carbon::createFromFormat('d/m/Y', trim($linha[$colunas['data_base']]))->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->percentagem = $this->paraDecimal($linha[$colunas['percentagem']]);
                            $model->tem_mo = strtolower(trim($linha[$colunas['tem_mo']])) === 'verdadeiro' ? 'sim' : 'nao';
                            $model->custo = $this->paraDecimal($linha[$colunas['custo']]);
                            break;

                        case 'materiais.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $model->data_base = \Carbon\Carbon::createFromFormat('d/m/Y', trim($linha[$colunas['data_base']]))->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->unidade = trim($linha[$colunas['unid']]);
                            $model->custo_unitario = $this->paraDecimal($linha[$colunas['custo_unitario']]);
                            $model->consumo = (float) str_replace(',', '.', $linha[$colunas['consumo']]);
                            $model->custo_unitario_final = $this->paraDecimal($linha[$colunas['custo_unitario_final']]);
                            break;

                        case 'servicos.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $model->data_base = \Carbon\Carbon::createFromFormat('d/m/Y', trim($linha[$colunas['data_base']]))->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->unidade = trim($linha[$colunas['unid']]);
                            $model->custo_unitario = $this->paraDecimal($linha[$colunas['custo_unitario']]);
                            $model->consumo = (float) str_replace(',', '.', $linha[$colunas['consumo']]);
                            $model->custo_unitario_final = $this->paraDecimal($linha[$colunas['custo_unitario_final']]);
                            break;
                            
                        case 'transportes.xlsx':
                            $model->codigo_servico = trim($linha[$colunas['codigo_servico']]);
                            $model->descricao_servico = trim($linha[$colunas['descricao_servico']]);
                            $model->unidade_servico = trim($linha[$colunas['unidade_servico']]);
                            $model->data_base = \Carbon\Carbon::createFromFormat('d/m/Y', trim($linha[$colunas['data_base']]))->format('Y-m-d');
                            $model->desoneracao = strtolower(trim($linha[$colunas['honerado']])) === 'com desoneração' ? 'com' : 'sem';
                            $model->descricao          = trim($linha[$colunas['descricao']]);
                            $model->codigo = trim($linha[$colunas['codigo']]);
                            $model->unidade = trim($linha[$colunas['unid']]);
                            $model->formula1 = trim($linha[$colunas['formula1']]);
                            $model->formula2 = trim($linha[$colunas['formula2']]);
                            $model->custo = $this->paraDecimal($linha[$colunas['custo']]);
                            $model->consumo = (float) str_replace(',', '.', $linha[$colunas['consumo']]);
                            $model->custo_unitario = $this->paraDecimal($linha[$colunas['custo_unitario']]);
                            break;
                            
                        case 'formulas_transporte.xlsx':
                            // A data já está no formato Y-m-d, não precisa converter
                            $model->data_base = trim($linha[$colunas['data_base']]);
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
                        case 'composicoes.xlsx':
                            $registroExistente = $config['model']::where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'equipamentos.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo_equipamento', $model->codigo_equipamento)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'mao_de_obra.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'itens_incidencia.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'materiais.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'servicos.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
                                ->where('data_base', $model->data_base)
                                ->where('desoneracao', $model->desoneracao)
                                ->first();
                            break;
                        case 'transportes.xlsx':
                            $registroExistente = $config['model']::where('codigo_servico', $model->codigo_servico)
                                ->where('codigo', $model->codigo)
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
                        'linha' => $index + 2, // +2 porque removemos o cabeçalho e o índice começa em 0
                        'erro' => $e->getMessage(),
                        'dados' => $linha
                    ];
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
            $this->logProgresso('EXCEL_FORMULAS_TRANSPORTE', 'Iniciando salvamento do arquivo Excel');
            
            // Obter diretório da sessão
            $nomeDiretorio = session('derpr_processamento_dir');
            if (!$nomeDiretorio) {
                $this->logErroValidacao('EXCEL_FORMULAS_TRANSPORTE', 'Diretório de processamento não encontrado na sessão');
                throw new \Exception('Diretório de processamento não encontrado na sessão');
            }
            
            $diretorioProcessamento = Storage::path('temp/' . $nomeDiretorio);
            
            // Criar diretório se não existir
            if (!file_exists($diretorioProcessamento)) {
                mkdir($diretorioProcessamento, 0755, true);
                $this->logProgresso('EXCEL_FORMULAS_TRANSPORTE', 'Diretório criado com sucesso');
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
            $caminhoArquivo = $diretorioProcessamento . '/derpr_formulas_transporte.xlsx';
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

            $this->logSucesso('EXCEL_FORMULAS_TRANSPORTE', [
                'arquivo_excel' => 'derpr_formulas_transporte.xlsx',
                'arquivo_metadata' => 'derpr_metadata_formulas_transporte.json',
                'total_registros' => count($dados),
                'diretorio' => $nomeDiretorio
            ]);

        } catch (\Exception $e) {
            $this->logErroCritico('EXCEL_FORMULAS_TRANSPORTE', $e->getMessage(), [
                'arquivo' => 'derpr_formulas_transporte.xlsx',
                'diretorio' => $nomeDiretorio ?? 'N/A'
            ]);
        }
    }

    /**
     * Log centralizado para importação DER-PR
     * 
     * @param string $level Nível do log (info, error, warning)
     * @param string $origin Origem da operação
     * @param string $message Mensagem descritiva
     * @param array $context Contexto adicional
     */
    private function logImportacaoDERPR($level, $origin, $message, $context = [])
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

        $formattedMessage = "[{$timestamp}] [DERPR] [{$origin}] [{$userInfo}] [IP: {$ip}] - {$message}";

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
     * Log de início de operação DER-PR
     */
    private function logInicioOperacao($operation, $context = [])
    {
        $this->logImportacaoDERPR('info', 'INICIO_OPERACAO', "Iniciando: {$operation}", $context);
    }

    /**
     * Log de progresso da operação DER-PR
     */
    private function logProgresso($operation, $status, $context = [])
    {
        $this->logImportacaoDERPR('info', 'PROGRESSO', "{$operation}: {$status}", $context);
    }

    /**
     * Log de sucesso da operação DER-PR
     */
    private function logSucesso($operation, $resultados = [], $context = [])
    {
        $this->logImportacaoDERPR('info', 'SUCESSO', "Concluído com sucesso: {$operation}", array_merge($resultados, $context));
    }

    /**
     * Log de erro de validação/dados DER-PR (não crítico)
     */
    private function logErroValidacao($operation, $error, $context = [])
    {
        $this->logImportacaoDERPR('error', 'VALIDACAO', "Erro de validação em {$operation}: {$error}", $context);
    }

    /**
     * Log de erro crítico do sistema DER-PR
     */
    private function logErroCritico($operation, $error, $context = [])
    {
        // Erro crítico vai para ambos os logs
        $this->logImportacaoDERPR('error', 'ERRO_CRITICO', "Erro crítico em {$operation}: {$error}", $context);
        
        // E também para o Laravel.log (erro crítico do sistema)
        Log::error("Erro crítico na importação DER-PR: {$operation} - {$error}", $context);
    }

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
