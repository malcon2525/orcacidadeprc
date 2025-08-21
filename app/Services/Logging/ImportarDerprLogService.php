<?php

namespace App\Services\Logging;

class ImportarDerprLogService extends LogService
{
    public function __construct()
    {
        parent::__construct('DERPR', 'tabela_oficial/importar_derpr.log');
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
        $this->inicioOperacao('GRAVACAO_BANCO_DERPR', $context);
    }
    
    /**
     * Log de progresso da gravação de arquivo
     */
    public function gravacaoArquivo($nomeArquivo)
    {
        $this->progresso('GRAVACAO_ARQUIVO', "Processando: {$nomeArquivo}");
    }
    
    /**
     * Log de erro de validação específico para DER-PR
     */
    public function erroValidacaoDerpr($operation, $error, $context = [])
    {
        $this->erroValidacao($operation, $error, $context);
    }
    
    /**
     * Log de erro crítico específico para DER-PR
     */
    public function erroCriticoDerpr($operation, $error, $context = [])
    {
        $this->erroCritico($operation, $error, $context);
    }
}
