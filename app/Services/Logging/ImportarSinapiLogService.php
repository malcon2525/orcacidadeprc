<?php

namespace App\Services\Logging;

class ImportarSinapiLogService extends LogService
{
    public function __construct()
    {
        parent::__construct('SINAPI', 'tabela_oficial/importar_sinapi.log');
    }
    
    /**
     * Log de início de aba específica
     */
    public function inicioAba($numero, $funcionalidade, $context = [])
    {
        $this->inicioOperacao("ABA_{$numero}_{$funcionalidade}", $context);
    }
    
    /**
     * Log de sucesso de aba específica
     */
    public function sucessoAba($numero, $funcionalidade, $context = [])
    {
        $this->sucesso("ABA_{$numero}_{$funcionalidade}", $context);
    }
    
    /**
     * Log de início da gravação no banco
     */
    public function gravacaoBanco($context = [])
    {
        $this->inicioOperacao('GRAVACAO_BANCO_SINAPI', $context);
    }
    
    /**
     * Log de progresso da gravação de arquivo
     */
    public function gravacaoArquivo($nomeArquivo)
    {
        $this->progresso('GRAVACAO_ARQUIVO', "Processando: {$nomeArquivo}");
    }
    
    /**
     * Log de erro de validação específico para SINAPI
     */
    public function erroValidacaoSinapi($operation, $error, $context = [])
    {
        $this->erroValidacao($operation, $error, $context);
    }
    
    /**
     * Log de erro crítico específico para SINAPI
     */
    public function erroCriticoSinapi($operation, $error, $context = [])
    {
        $this->erroCritico($operation, $error, $context);
    }
    
    /**
     * Log de processamento de arquivo específico
     */
    public function processamentoArquivo($nomeArquivo, $status, $context = [])
    {
        $this->progresso('PROCESSAMENTO_ARQUIVO', "Arquivo: {$nomeArquivo} | Status: {$status}", $context);
    }
    
    /**
     * Log de verificação de arquivos
     */
    public function verificacaoArquivos($context = [])
    {
        $this->inicioOperacao('VERIFICACAO_ARQUIVOS', $context);
    }
    
    /**
     * Log de sucesso na verificação de arquivos
     */
    public function sucessoVerificacaoArquivos($context = [])
    {
        $this->sucesso('VERIFICACAO_ARQUIVOS', $context);
    }
}
