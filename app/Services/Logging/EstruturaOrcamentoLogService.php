<?php

namespace App\Services\Logging;

class EstruturaOrcamentoLogService extends LogService
{
    public function __construct()
    {
        parent::__construct('ESTRUTURA_ORCAMENTO', 'administracao/estrutura_orcamento.log');
    }

    public function inicioImportacao($tipoOrcamentoId, $context = [])
    {
        $this->inicioOperacao('IMPORTACAO_ESTRUTURA', array_merge([
            'tipo_orcamento_id' => $tipoOrcamentoId
        ], $context));
    }

    public function sucessoImportacao($context = [])
    {
        $this->sucesso('IMPORTACAO_ESTRUTURA', $context);
    }

    public function erroImportacao($error, $context = [])
    {
        $this->erroCritico('IMPORTACAO_ESTRUTURA', $error, $context);
    }

    public function processamentoArquivo($detalhes)
    {
        $this->progresso('PROCESSAMENTO_ARQUIVO', 'Processando arquivo Excel', $detalhes);
    }

    public function processamentoGrandesItens($totalProcessados)
    {
        $this->progresso('GRANDES_ITENS', "Processados {$totalProcessados} grandes itens");
    }

    public function processamentoSubgrupos($totalProcessados)
    {
        $this->progresso('SUBGRUPOS', "Processados {$totalProcessados} subgrupos");
    }

    public function limpezaAnterior($tipoOrcamentoId)
    {
        $this->progresso('LIMPEZA_DADOS_ANTERIORES', "Removendo dados anteriores do tipo de orçamento {$tipoOrcamentoId}");
    }
}
