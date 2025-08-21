<?php

namespace App\Services\Logging;

class EntidadesOrcamentariasLogService extends LogService
{
    public function __construct()
    {
        parent::__construct('ENTIDADES_ORCAMENTARIAS', 'administracao/entidades_orcamentarias.log');
    }
    
    /**
     * Log de acesso à funcionalidade
     */
    public function acessoFuncionalidade($tipo, $context = [])
    {
        $this->inicioOperacao("ACESSO_{$tipo}", array_merge([
            'funcionalidade' => $tipo
        ], $context));
    }
    
    /**
     * Log de sucesso no acesso
     */
    public function sucessoAcessoFuncionalidade($tipo, $context = [])
    {
        $this->sucesso("ACESSO_{$tipo}", array_merge([
            'funcionalidade' => $tipo
        ], $context));
    }
    
    /**
     * Log de acesso negado
     */
    public function acessoNegadoFuncionalidade($tipo, $motivo, $context = [])
    {
        $this->erroCritico("ACESSO_NEGADO_{$tipo}", $motivo, array_merge([
            'funcionalidade' => $tipo
        ], $context));
    }
    
    /**
     * Log de início de listagem
     */
    public function inicioListagem($filtros, $context = [])
    {
        $this->inicioOperacao('LISTAGEM_ENTIDADES', array_merge([
            'filtros' => $filtros
        ], $context));
    }
    
    /**
     * Log de sucesso na listagem
     */
    public function sucessoListagem($total, $filtros, $context = [])
    {
        $this->sucesso('LISTAGEM_ENTIDADES', array_merge([
            'total_registros' => $total,
            'filtros' => $filtros
        ], $context));
    }
    
    /**
     * Log de início de criação
     */
    public function inicioCriacao($dados, $context = [])
    {
        $this->inicioOperacao('CRIACAO_ENTIDADE', array_merge([
            'razao_social' => $dados['razao_social'] ?? 'N/A',
            'nome_fantasia' => $dados['nome_fantasia'] ?? 'N/A',
            'tipo_organizacao' => $dados['tipo_organizacao'] ?? 'N/A'
        ], $context));
    }
    
    /**
     * Log de sucesso na criação
     */
    public function sucessoCriacao($entidadeId, $dados, $context = [])
    {
        $this->sucesso('CRIACAO_ENTIDADE', array_merge([
            'entidade_id' => $entidadeId,
            'razao_social' => $dados['razao_social'] ?? 'N/A',
            'nome_fantasia' => $dados['nome_fantasia'] ?? 'N/A',
            'tipo_organizacao' => $dados['tipo_organizacao'] ?? 'N/A'
        ], $context));
    }
    
    /**
     * Log de início de edição
     */
    public function inicioEdicao($entidadeId, $dados, $context = [])
    {
        $this->inicioOperacao('EDICAO_ENTIDADE', array_merge([
            'entidade_id' => $entidadeId,
            'razao_social' => $dados['razao_social'] ?? 'N/A',
            'nome_fantasia' => $dados['nome_fantasia'] ?? 'N/A'
        ], $context));
    }
    
    /**
     * Log de sucesso na edição
     */
    public function sucessoEdicao($entidadeId, $dados, $context = [])
    {
        $this->sucesso('EDICAO_ENTIDADE', array_merge([
            'entidade_id' => $entidadeId,
            'razao_social' => $dados['razao_social'] ?? 'N/A',
            'nome_fantasia' => $dados['nome_fantasia'] ?? 'N/A'
        ], $context));
    }
    
    /**
     * Log de início de exclusão
     */
    public function inicioExclusao($entidadeId, $context = [])
    {
        $this->inicioOperacao('EXCLUSAO_ENTIDADE', array_merge([
            'entidade_id' => $entidadeId
        ], $context));
    }
    
    /**
     * Log de sucesso na exclusão
     */
    public function sucessoExclusao($entidadeId, $context = [])
    {
        $this->sucesso('EXCLUSAO_ENTIDADE', array_merge([
            'entidade_id' => $entidadeId
        ], $context));
    }
    
    /**
     * Log de início de importação
     */
    public function inicioImportacao($context = [])
    {
        $this->inicioOperacao('IMPORTACAO_MUNICIPIOS', $context);
    }
    
    /**
     * Log de sucesso na importação
     */
    public function sucessoImportacao($novos, $atualizados, $context = [])
    {
        $this->sucesso('IMPORTACAO_MUNICIPIOS', array_merge([
            'novos_registros' => $novos,
            'registros_atualizados' => $atualizados
        ], $context));
    }
    
    /**
     * Log de erro de validação
     */
    public function erroValidacao($operacao, $erros, $context = [])
    {
        parent::erroValidacao("VALIDACAO_{$operacao}", 'Falha na validação dos dados', array_merge([
            'erros' => $erros
        ], $context));
    }
    
    /**
     * Log de erro crítico
     */
    public function erroCritico($operacao, $mensagem, $context = [])
    {
        parent::erroCritico("ERRO_{$operacao}", $mensagem, $context);
    }
}
