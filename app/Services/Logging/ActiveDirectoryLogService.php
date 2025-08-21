<?php

namespace App\Services\Logging;

class ActiveDirectoryLogService extends LogService
{
    public function __construct()
    {
        parent::__construct('ACTIVE_DIRECTORY', 'administracao/active_directory.log');
    }
    
    // ========================================
    // LOGS DE ACESSO À FUNCIONALIDADE
    // ========================================
    
    /**
     * Log de acesso à funcionalidade Active Directory
     */
    public function acessoFuncionalidade($tipo, $context = [])
    {
        $this->inicioOperacao("ACESSO_{$tipo}", array_merge([
            'funcionalidade' => $tipo
        ], $context));
    }
    
    /**
     * Log de sucesso no acesso à funcionalidade
     */
    public function sucessoAcessoFuncionalidade($tipo, $context = [])
    {
        $this->sucesso("ACESSO_{$tipo}", array_merge([
            'funcionalidade' => $tipo
        ], $context));
    }
    
    /**
     * Log de acesso negado à funcionalidade
     */
    public function acessoNegadoFuncionalidade($tipo, $context = [])
    {
        $this->erroCritico("ACESSO_NEGADO_{$tipo}", 'Acesso negado - permissão insuficiente', array_merge([
            'funcionalidade' => $tipo
        ], $context));
    }
    
    // ========================================
    // LOGS DE CONFIGURAÇÃO
    // ========================================
    
    /**
     * Log de consulta às configurações
     */
    public function consultaConfiguracoes($context = [])
    {
        $this->inicioOperacao('CONSULTA_CONFIGURACOES', $context);
    }
    
    /**
     * Log de sucesso na consulta às configurações
     */
    public function sucessoConsultaConfiguracoes($configuracoes, $context = [])
    {
        $this->sucesso('CONSULTA_CONFIGURACOES', array_merge([
            'configuracoes' => $configuracoes
        ], $context));
    }
    
    /**
     * Log de alteração de configurações
     */
    public function alteracaoConfiguracoes($configAnteriores, $configNovas, $context = [])
    {
        $this->inicioOperacao('ALTERACAO_CONFIGURACOES', array_merge([
            'configuracoes_anteriores' => $configAnteriores,
            'configuracoes_novas' => $configNovas
        ], $context));
    }
    
    /**
     * Log de sucesso na alteração de configurações
     */
    public function sucessoAlteracaoConfiguracoes($configAnteriores, $configNovas, $context = [])
    {
        $this->sucesso('ALTERACAO_CONFIGURACOES', array_merge([
            'configuracoes_anteriores' => $configAnteriores,
            'configuracoes_novas' => $configNovas
        ], $context));
    }
    
    /**
     * Log de teste de configurações
     */
    public function testeConfiguracoes($context = [])
    {
        $this->inicioOperacao('TESTE_CONFIGURACOES', $context);
    }
    
    /**
     * Log de sucesso no teste de configurações
     */
    public function sucessoTesteConfiguracoes($resultados, $context = [])
    {
        $this->sucesso('TESTE_CONFIGURACOES', array_merge([
            'resultados' => $resultados
        ], $context));
    }
    
    // ========================================
    // LOGS DE SINCRONIZAÇÃO
    // ========================================
    
    /**
     * Log de início de sincronização
     */
    public function inicioSincronizacao($tipo, $context = [])
    {
        $this->inicioOperacao("SINCRONIZACAO_{$tipo}", array_merge([
            'tipo_sincronizacao' => $tipo
        ], $context));
    }
    
    /**
     * Log de sucesso na sincronização
     */
    public function sucessoSincronizacao($tipo, $resultados, $context = [])
    {
        $this->sucesso("SINCRONIZACAO_{$tipo}", array_merge([
            'tipo_sincronizacao' => $tipo,
            'resultados' => $resultados
        ], $context));
    }
    
    /**
     * Log de progresso da sincronização
     */
    public function progressoSincronizacao($tipo, $status, $context = [])
    {
        $this->progresso("SINCRONIZACAO_{$tipo}", $status, $context);
    }
    
    /**
     * Log de verificação de status da sincronização
     */
    public function verificarStatusSincronizacao($context = [])
    {
        $this->inicioOperacao('VERIFICAR_STATUS_SINCRONIZACAO', $context);
    }
    
    /**
     * Log de sucesso na verificação de status
     */
    public function sucessoVerificarStatusSincronizacao($estatisticas, $context = [])
    {
        $this->sucesso('VERIFICAR_STATUS_SINCRONIZACAO', array_merge([
            'estatisticas' => $estatisticas
        ], $context));
    }
    
    // ========================================
    // LOGS DE CONEXÃO
    // ========================================
    
    /**
     * Log de teste de conexão
     */
    public function testeConexao($context = [])
    {
        $this->inicioOperacao('TESTE_CONEXAO', $context);
    }
    
    /**
     * Log de sucesso no teste de conexão
     */
    public function sucessoTesteConexao($resultados, $context = [])
    {
        $this->sucesso('TESTE_CONEXAO', array_merge([
            'resultados' => $resultados
        ], $context));
    }
    
    /**
     * Log de tentativa de conexão
     */
    public function tentativaConexao($host, $port, $context = [])
    {
        $this->inicioOperacao('TENTATIVA_CONEXAO', array_merge([
            'host' => $host,
            'port' => $port
        ], $context));
    }
    
    /**
     * Log de sucesso na conexão
     */
    public function sucessoConexao($host, $port, $context = [])
    {
        $this->sucesso('CONEXAO_ESTABELECIDA', array_merge([
            'host' => $host,
            'port' => $port
        ], $context));
    }
    
    /**
     * Log de falha na conexão
     */
    public function falhaConexao($host, $port, $erro, $context = [])
    {
        $this->erroCritico('FALHA_CONEXAO', $erro, array_merge([
            'host' => $host,
            'port' => $port
        ], $context));
    }
    
    // ========================================
    // LOGS DE OPERAÇÕES LDAP
    // ========================================
    
    /**
     * Log de busca de usuários no AD
     */
    public function buscaUsuariosAD($filtro, $context = [])
    {
        $this->inicioOperacao('BUSCA_USUARIOS_AD', array_merge([
            'filtro' => $filtro
        ], $context));
    }
    
    /**
     * Log de sucesso na busca de usuários
     */
    public function sucessoBuscaUsuariosAD($quantidade, $context = [])
    {
        $this->sucesso('BUSCA_USUARIOS_AD', array_merge([
            'usuarios_encontrados' => $quantidade
        ], $context));
    }
    
    /**
     * Log de sincronização de usuário específico
     */
    public function sincronizacaoUsuario($username, $acao, $context = [])
    {
        $this->inicioOperacao('SINCRONIZACAO_USUARIO', array_merge([
            'username' => $username,
            'acao' => $acao
        ], $context));
    }
    
    /**
     * Log de sucesso na sincronização de usuário
     */
    public function sucessoSincronizacaoUsuario($username, $acao, $resultado, $context = [])
    {
        $this->sucesso('SINCRONIZACAO_USUARIO', array_merge([
            'username' => $username,
            'acao' => $acao,
            'resultado' => $resultado
        ], $context));
    }
    
    // ========================================
    // LOGS DE AUTENTICAÇÃO
    // ========================================
    
    /**
     * Log de tentativa de autenticação no AD
     */
    public function tentativaAutenticacaoAD($username, $formats, $tentativa = null, $context = [])
    {
        $this->inicioOperacao('TENTATIVA_AUTENTICACAO_AD', array_merge([
            'username' => $username,
            'formats' => $formats,
            'tentativa' => $tentativa
        ], $context));
    }
    
    /**
     * Log de sucesso na autenticação no AD
     */
    public function sucessoAutenticacaoAD($username, $userDn, $context = [])
    {
        $this->sucesso('AUTENTICACAO_AD', array_merge([
            'username' => $username,
            'user_dn' => $userDn
        ], $context));
    }
    
    /**
     * Log de falha na autenticação no AD
     */
    public function falhaAutenticacaoAD($username, $userDn, $erro, $context = [])
    {
        $this->erroCritico('FALHA_AUTENTICACAO_AD', $erro, array_merge([
            'username' => $username,
            'user_dn' => $userDn
        ], $context));
    }
    
    // ========================================
    // LOGS DE ERROS
    // ========================================
    
    /**
     * Log de erro de validação
     */
    public function erroValidacao($operacao, $erros, $context = [])
    {
        $this->erroValidacao($operacao, $erros, $context);
    }
    
    /**
     * Log de erro crítico
     */
    public function erroCritico($operacao, $mensagem, $context = [])
    {
        $this->erroCritico($operacao, $mensagem, $context);
    }
}
