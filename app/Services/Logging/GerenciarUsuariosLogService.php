<?php

namespace App\Services\Logging;

class GerenciarUsuariosLogService extends LogService
{
    public function __construct()
    {
        parent::__construct('USUARIOS', 'administracao/gerenciar_usuarios.log');
    }
    
    // ========================================
    // LOGS DE USUÁRIOS (JÁ IMPLEMENTADOS)
    // ========================================
    
    /**
     * Log de criação de usuário
     */
    public function criacaoUsuario($userId, $userData, $context = [])
    {
        $this->inicioOperacao('CRIACAO_USUARIO', array_merge([
            'usuario_id' => $userId,
            'email' => $userData['email'] ?? 'N/A',
            'nome' => $userData['name'] ?? 'N/A',
            'tipo_login' => $userData['login_type'] ?? 'N/A',
            'papel_inicial' => $userData['roles'] ?? []
        ], $context));
    }
    
    /**
     * Log de sucesso na criação de usuário
     */
    public function sucessoCriacaoUsuario($userId, $userData, $context = [])
    {
        $this->sucesso('CRIACAO_USUARIO', array_merge([
            'usuario_id' => $userId,
            'email' => $userData['email'] ?? 'N/A',
            'nome' => $userData['name'] ?? 'N/A',
            'tipo_login' => $userData['login_type'] ?? 'N/A',
            'papel_inicial' => $userData['roles'] ?? []
        ], $context));
    }
    
    /**
     * Log de edição de usuário
     */
    public function edicaoUsuario($userId, $dadosAnteriores, $dadosNovos, $context = [])
    {
        $this->inicioOperacao('EDICAO_USUARIO', array_merge([
            'usuario_id' => $userId,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos
        ], $context));
    }
    
    /**
     * Log de sucesso na edição de usuário
     */
    public function sucessoEdicaoUsuario($userId, $dadosAnteriores, $dadosNovos, $context = [])
    {
        $this->sucesso('EDICAO_USUARIO', array_merge([
            'usuario_id' => $userId,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos
        ], $context));
    }
    
    /**
     * Log de alteração de papéis de usuário
     */
    public function alteracaoPapeis($userId, $papeisAnteriores, $papeisNovos, $context = [])
    {
        $this->inicioOperacao('ALTERACAO_PAPEIS_USUARIO', array_merge([
            'usuario_id' => $userId,
            'papeis_anteriores' => $papeisAnteriores,
            'papeis_novos' => $papeisNovos
        ], $context));
    }
    
    /**
     * Log de sucesso na alteração de papéis de usuário
     */
    public function sucessoAlteracaoPapeis($userId, $papeisAnteriores, $papeisNovos, $context = [])
    {
        $this->sucesso('ALTERACAO_PAPEIS_USUARIO', array_merge([
            'usuario_id' => $userId,
            'papeis_anteriores' => $papeisAnteriores,
            'papeis_novos' => $papeisNovos
        ], $context));
    }
    
    /**
     * Log de alteração de senha de usuário
     */
    public function alteracaoSenha($userId, $context = [])
    {
        $this->inicioOperacao('ALTERACAO_SENHA_USUARIO', array_merge([
            'usuario_id' => $userId
        ], $context));
    }
    
    /**
     * Log de sucesso na alteração de senha de usuário
     */
    public function sucessoAlteracaoSenha($userId, $context = [])
    {
        $this->sucesso('ALTERACAO_SENHA_USUARIO', array_merge([
            'usuario_id' => $userId
        ], $context));
    }
    
    /**
     * Log de exclusão de usuário
     */
    public function exclusaoUsuario($userId, $dadosUsuario, $context = [])
    {
        $this->inicioOperacao('EXCLUSAO_USUARIO', array_merge([
            'usuario_id' => $userId,
            'dados_usuario' => $dadosUsuario
        ], $context));
    }
    
    /**
     * Log de sucesso na exclusão de usuário
     */
    public function sucessoExclusaoUsuario($userId, $dadosUsuario, $context = [])
    {
        $this->sucesso('EXCLUSAO_USUARIO', array_merge([
            'usuario_id' => $userId,
            'dados_usuario' => $dadosUsuario
        ], $context));
    }
    
    /**
     * Log de alteração de status de usuário
     */
    public function alteracaoStatus($userId, $statusAnterior, $statusNovo, $context = [])
    {
        $this->inicioOperacao('ALTERACAO_STATUS_USUARIO', array_merge([
            'usuario_id' => $userId,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo
        ], $context));
    }
    
    /**
     * Log de sucesso na alteração de status de usuário
     */
    public function sucessoAlteracaoStatus($userId, $statusAnterior, $statusNovo, $context = [])
    {
        $this->sucesso('ALTERACAO_STATUS_USUARIO', array_merge([
            'usuario_id' => $userId,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo
        ], $context));
    }
    
    // ========================================
    // LOGS DE PAPÉIS (NOVOS)
    // ========================================
    
    /**
     * Log de criação de papel
     */
    public function criacaoPapel($papelId, $papelData, $context = [])
    {
        $this->inicioOperacao('CRIACAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'nome' => $papelData['name'] ?? 'N/A',
            'nome_exibicao' => $papelData['display_name'] ?? 'N/A',
            'descricao' => $papelData['description'] ?? 'N/A',
            'ativo' => $papelData['is_active'] ?? true
        ], $context));
    }
    
    /**
     * Log de sucesso na criação de papel
     */
    public function sucessoCriacaoPapel($papelId, $papelData, $context = [])
    {
        $this->sucesso('CRIACAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'nome' => $papelData['name'] ?? 'N/A',
            'nome_exibicao' => $papelData['display_name'] ?? 'N/A',
            'descricao' => $papelData['description'] ?? 'N/A',
            'ativo' => $papelData['is_active'] ?? true
        ], $context));
    }
    
    /**
     * Log de edição de papel
     */
    public function edicaoPapel($papelId, $dadosAnteriores, $dadosNovos, $context = [])
    {
        $this->inicioOperacao('EDICAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos
        ], $context));
    }
    
    /**
     * Log de sucesso na edição de papel
     */
    public function sucessoEdicaoPapel($papelId, $dadosAnteriores, $dadosNovos, $context = [])
    {
        $this->sucesso('EDICAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos
        ], $context));
    }
    
    /**
     * Log de exclusão de papel
     */
    public function exclusaoPapel($papelId, $dadosPapel, $context = [])
    {
        $this->inicioOperacao('EXCLUSAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'nome' => $dadosPapel['name'] ?? 'N/A',
            'nome_exibicao' => $dadosPapel['display_name'] ?? 'N/A'
        ], $context));
    }
    
    /**
     * Log de sucesso na exclusão de papel
     */
    public function sucessoExclusaoPapel($papelId, $dadosPapel, $context = [])
    {
        $this->sucesso('EXCLUSAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'nome' => $dadosPapel['name'] ?? 'N/A',
            'nome_exibicao' => $dadosPapel['display_name'] ?? 'N/A'
        ], $context));
    }
    
    /**
     * Log de atribuição de permissão ao papel
     */
    public function atribuicaoPermissaoPapel($papelId, $permissaoId, $context = [])
    {
        $this->inicioOperacao('ATRIBUICAO_PERMISSAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'permissao_id' => $permissaoId
        ], $context));
    }
    
    /**
     * Log de sucesso na atribuição de permissão ao papel
     */
    public function sucessoAtribuicaoPermissaoPapel($papelId, $permissaoId, $context = [])
    {
        $this->sucesso('ATRIBUICAO_PERMISSAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'permissao_id' => $permissaoId
        ], $context));
    }
    
    /**
     * Log de remoção de permissão do papel
     */
    public function remocaoPermissaoPapel($papelId, $permissaoId, $context = [])
    {
        $this->inicioOperacao('REMOCAO_PERMISSAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'permissao_id' => $permissaoId
        ], $context));
    }
    
    /**
     * Log de sucesso na remoção de permissão do papel
     */
    public function sucessoRemocaoPermissaoPapel($papelId, $permissaoId, $context = [])
    {
        $this->sucesso('REMOCAO_PERMISSAO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'permissao_id' => $permissaoId
        ], $context));
    }
    
    /**
     * Log de sincronização de permissões do papel
     */
    public function sincronizacaoPermissoesPapel($papelId, $permissoesAnteriores, $permissoesNovas, $context = [])
    {
        $this->inicioOperacao('SINCRONIZACAO_PERMISSOES_PAPEL', array_merge([
            'papel_id' => $papelId,
            'permissoes_anteriores' => $permissoesAnteriores,
            'permissoes_novas' => $permissoesNovas
        ], $context));
    }
    
    /**
     * Log de sucesso na sincronização de permissões do papel
     */
    public function sucessoSincronizacaoPermissoesPapel($papelId, $permissoesAnteriores, $permissoesNovas, $context = [])
    {
        $this->sucesso('SINCRONIZACAO_PERMISSOES_PAPEL', array_merge([
            'papel_id' => $papelId,
            'permissoes_anteriores' => $permissoesAnteriores,
            'permissoes_novas' => $permissoesNovas
        ], $context));
    }
    
    /**
     * Log de adição de usuário ao papel
     */
    public function adicaoUsuarioPapel($papelId, $userId, $context = [])
    {
        $this->inicioOperacao('ADICAO_USUARIO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'usuario_id' => $userId
        ], $context));
    }
    
    /**
     * Log de sucesso na adição de usuário ao papel
     */
    public function sucessoAdicaoUsuarioPapel($papelId, $userId, $context = [])
    {
        $this->sucesso('ADICAO_USUARIO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'usuario_id' => $userId
        ], $context));
    }
    
    /**
     * Log de remoção de usuário do papel
     */
    public function remocaoUsuarioPapel($papelId, $userId, $context = [])
    {
        $this->inicioOperacao('REMOCAO_USUARIO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'usuario_id' => $userId
        ], $context));
    }
    
    /**
     * Log de sucesso na remoção de usuário do papel
     */
    public function sucessoRemocaoUsuarioPapel($papelId, $userId, $context = [])
    {
        $this->sucesso('REMOCAO_USUARIO_PAPEL', array_merge([
            'papel_id' => $papelId,
            'usuario_id' => $userId
        ], $context));
    }
    
    // ========================================
    // LOGS DE PERMISSÕES (NOVOS)
    // ========================================
    
    /**
     * Log de criação de permissão
     */
    public function criacaoPermissao($permissaoId, $permissaoData, $context = [])
    {
        $this->inicioOperacao('CRIACAO_PERMISSAO', array_merge([
            'permissao_id' => $permissaoId,
            'nome' => $permissaoData['name'] ?? 'N/A',
            'nome_exibicao' => $permissaoData['display_name'] ?? 'N/A',
            'descricao' => $permissaoData['description'] ?? 'N/A',
            'ativo' => $permissaoData['is_active'] ?? true
        ], $context));
    }
    
    /**
     * Log de sucesso na criação de permissão
     */
    public function sucessoCriacaoPermissao($permissaoId, $permissaoData, $context = [])
    {
        $this->sucesso('CRIACAO_PERMISSAO', array_merge([
            'permissao_id' => $permissaoId,
            'nome' => $permissaoData['name'] ?? 'N/A',
            'nome_exibicao' => $permissaoData['display_name'] ?? 'N/A',
            'descricao' => $permissaoData['description'] ?? 'N/A',
            'ativo' => $permissaoData['is_active'] ?? true
        ], $context));
    }
    
    /**
     * Log de edição de permissão
     */
    public function edicaoPermissao($permissaoId, $dadosAnteriores, $dadosNovos, $context = [])
    {
        $this->inicioOperacao('EDICAO_PERMISSAO', array_merge([
            'permissao_id' => $permissaoId,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos
        ], $context));
    }
    
    /**
     * Log de sucesso na edição de permissão
     */
    public function sucessoEdicaoPermissao($permissaoId, $dadosAnteriores, $dadosNovos, $context = [])
    {
        $this->sucesso('EDICAO_PERMISSAO', array_merge([
            'permissao_id' => $permissaoId,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos
        ], $context));
    }
    
    /**
     * Log de exclusão de permissão
     */
    public function exclusaoPermissao($permissaoId, $dadosPermissao, $context = [])
    {
        $this->inicioOperacao('EXCLUSAO_PERMISSAO', array_merge([
            'permissao_id' => $permissaoId,
            'nome' => $dadosPermissao['name'] ?? 'N/A',
            'nome_exibicao' => $dadosPermissao['display_name'] ?? 'N/A'
        ], $context));
    }
    
    /**
     * Log de sucesso na exclusão de permissão
     */
    public function sucessoExclusaoPermissao($permissaoId, $dadosPermissao, $context = [])
    {
        $this->sucesso('EXCLUSAO_PERMISSAO', array_merge([
            'permissao_id' => $permissaoId,
            'nome' => $dadosPermissao['name'] ?? 'N/A',
            'nome_exibicao' => $dadosPermissao['display_name'] ?? 'N/A'
        ], $context));
    }
    
    /**
     * Log de vinculação de permissão ao papel
     */
    public function vinculacaoPermissaoPapel($permissaoId, $papelId, $context = [])
    {
        $this->inicioOperacao('VINCULACAO_PERMISSAO_PAPEL', array_merge([
            'permissao_id' => $permissaoId,
            'papel_id' => $papelId
        ], $context));
    }
    
    /**
     * Log de sucesso na vinculação de permissão ao papel
     */
    public function sucessoVinculacaoPermissaoPapel($permissaoId, $papelId, $context = [])
    {
        $this->sucesso('VINCULACAO_PERMISSAO_PAPEL', array_merge([
            'permissao_id' => $permissaoId,
            'papel_id' => $papelId
        ], $context));
    }
    
    /**
     * Log de desvinculação de permissão do papel
     */
    public function desvinculacaoPermissaoPapel($permissaoId, $papelId, $context = [])
    {
        $this->inicioOperacao('DESVINCULACAO_PERMISSAO_PAPEL', array_merge([
            'permissao_id' => $permissaoId,
            'papel_id' => $papelId
        ], $context));
    }
    
    /**
     * Log de sucesso na desvinculação de permissão do papel
     */
    public function sucessoDesvinculacaoPermissaoPapel($permissaoId, $papelId, $context = [])
    {
        $this->sucesso('DESVINCULACAO_PERMISSAO_PAPEL', array_merge([
            'permissao_id' => $permissaoId,
            'papel_id' => $papelId
        ], $context));
    }
    
    // ========================================
    // LOGS DE ERROS (JÁ IMPLEMENTADOS)
    // ========================================
    
    /**
     * Log de erro de validação
     */
    public function erroValidacaoUsuarios($operacao, $erros, $context = [])
    {
        $this->erroValidacao($operacao, $erros, $context);
    }
    
    /**
     * Log de erro crítico
     */
    public function erroCriticoUsuarios($operacao, $mensagem, $context = [])
    {
        $this->erroCritico($operacao, $mensagem, $context);
    }
}
