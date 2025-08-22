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

            return response()->json([
                'success' => true,
                'message' => 'Estrutura importada com sucesso!',
                'data' => [
                    'tipo_orcamento' => $tipoOrcamento->descricao,
                    'grandes_itens_criados' => $resultado['grandes_itens'],
                    'subgrupos_criados' => $resultado['subgrupos']
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
        $spreadsheet = IOFactory::load($arquivo->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Remover cabeçalho
        $headers = array_shift($rows);
        
        // Validar cabeçalhos
        $this->validarCabecalhos($headers);

        $estrutura = [];
        $grandeItemAtual = null;

        foreach ($rows as $row) {
            if (empty(array_filter($row))) continue; // Pular linhas vazias

            $grandeItemOrdem = (int) $row[0];
            $grandeItemDescricao = trim($row[1]);
            $subgrupoOrdem = (int) $row[2];
            $subgrupoDescricao = trim($row[3]);

            // Se é um novo grande item
            if ($grandeItemOrdem !== $grandeItemAtual) {
                $grandeItemAtual = $grandeItemOrdem;
                $estrutura[$grandeItemOrdem] = [
                    'ordem' => $grandeItemOrdem,
                    'descricao' => $grandeItemDescricao,
                    'subgrupos' => []
                ];
            }

            // Adicionar subgrupo
            if ($subgrupoDescricao) {
                $estrutura[$grandeItemOrdem]['subgrupos'][] = [
                    'ordem' => $subgrupoOrdem,
                    'descricao' => $subgrupoDescricao
                ];
            }
        }

        return array_values($estrutura);
    }

    /**
     * Valida cabeçalhos do Excel
     */
    private function validarCabecalhos($headers)
    {
        $cabecalhosEsperados = [
            'grande_item_ordem',
            'grande_item_descricao', 
            'subgrupo_ordem',
            'subgrupo_descricao'
        ];

        foreach ($cabecalhosEsperados as $cabecalho) {
            if (!in_array($cabecalho, array_map('strtolower', $headers))) {
                throw new \Exception("Cabeçalho obrigatório não encontrado: {$cabecalho}");
            }
        }
    }

    /**
     * Valida estrutura dos dados extraídos
     */
    private function validarEstruturaDados($dados)
    {
        if (empty($dados)) {
            throw new \Exception('Nenhum dado encontrado no arquivo');
        }

        foreach ($dados as $grandeItem) {
            if (empty($grandeItem['descricao'])) {
                throw new \Exception('Descrição do grande item não pode estar vazia');
            }

            if (empty($grandeItem['subgrupos'])) {
                throw new \Exception("Grande item '{$grandeItem['descricao']}' deve ter pelo menos um subgrupo");
            }

            foreach ($grandeItem['subgrupos'] as $subgrupo) {
                if (empty($subgrupo['descricao'])) {
                    throw new \Exception('Descrição do subgrupo não pode estar vazia');
                }
            }
        }
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

        foreach ($dados as $grandeItemData) {
            // Criar grande item
            $grandeItem = GrandeItem::create([
                'eo_tipo_orcamento_id' => $tipoOrcamentoId,
                'descricao' => $grandeItemData['descricao'],
                'ordem' => $grandeItemData['ordem']
            ]);

            $grandesItensCriados++;

            // Criar subgrupos
            foreach ($grandeItemData['subgrupos'] as $subgrupoData) {
                SubGrupo::create([
                    'eo_grande_item_id' => $grandeItem->id,
                    'descricao' => $subgrupoData['descricao'],
                    'ordem' => $subgrupoData['ordem']
                ]);

                $subgruposCriados++;
            }
        }

        return [
            'grandes_itens' => $grandesItensCriados,
            'subgrupos' => $subgruposCriados
        ];
    }
}
