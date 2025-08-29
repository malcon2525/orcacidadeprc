<?php

namespace App\Helpers;

use App\Models\Orcamento\UserOrcamentoContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrcamentoContextHelper
{
    /**
     * Verifica se o usuário atual precisa definir contexto orçamentário
     */
    public static function precisaDefinirContexto(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Verificar se o usuário tem papel 'criar_orcamentos'
        if (!$user->hasRole('criar_orcamentos')) {
            return false; // Usuário não precisa de contexto
        }
        
        // Verificar se o usuário tem vínculos ativos com entidades orçamentárias
        $temVinculos = DB::table('user_entidades_orcamentarias')
            ->where('user_id', $user->id)
            ->where('ativo', true)
            ->exists();
        
        if (!$temVinculos) {
            return false; // Sem vínculos, não pode definir contexto
        }
        
        // Verificar se o contexto está definido
        return !UserOrcamentoContext::usuarioTemContexto($user->id);
    }
    
    /**
     * Retorna o contexto atual do usuário
     */
    public static function getContextoAtual(): ?UserOrcamentoContext
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }
        
        return UserOrcamentoContext::getContextoUsuario($user->id);
    }
    
    /**
     * Verifica se o usuário tem contexto definido
     */
    public static function temContextoDefinido(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return UserOrcamentoContext::usuarioTemContexto($user->id);
    }
    
    /**
     * Carrega o contexto na sessão (se existe)
     */
    public static function carregarContextoNaSessao(): void
    {
        $contexto = self::getContextoAtual();
        
        if ($contexto) {
            session([
                'orcamento_context' => [
                    'entidade_id' => $contexto->entidade_orcamentaria_id,
                    'entidade_nome' => $contexto->entidadeOrcamentaria->jurisdicao_razao_social,
                    'data_base_sinapi' => $contexto->data_base_sinapi->format('Y-m-d'),
                    'data_base_derpr' => $contexto->data_base_derpr->format('Y-m-d'),
                    'carregado_em' => now()->format('Y-m-d H:i:s')
                ]
            ]);
        }
    }
    
    /**
     * Remove o contexto da sessão
     */
    public static function limparContextoDaSessao(): void
    {
        session()->forget('orcamento_context');
    }
    
    /**
     * Retorna dados do contexto da sessão
     */
    public static function getContextoDaSessao(): ?array
    {
        return session('orcamento_context');
    }
    
    /**
     * Verifica se o usuário atual pode acessar menus de orçamento
     */
    public static function podeAcessarModuloOrcamento(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Super admin sempre pode acessar
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // Verificar se tem papel para acessar orçamento
        return $user->hasRole('criar_orcamentos');
    }
    
    /**
     * Retorna URL para configuração de contexto
     */
    public static function getUrlConfiguracaoContexto(): string
    {
        return route('orcamento.configuracao-orcamento.index');
    }
    
    /**
     * Middleware check - verifica contexto antes de acessar módulo orçamento
     */
    public static function verificarContextoParaModuloOrcamento(): ?string
    {
        // Se não pode acessar módulo orçamento, retorna erro de permissão
        if (!self::podeAcessarModuloOrcamento()) {
            return 'Acesso negado ao módulo orçamento.';
        }
        
        // Se precisa definir contexto, retorna URL de configuração
        if (self::precisaDefinirContexto()) {
            return self::getUrlConfiguracaoContexto();
        }
        
        // Carregar contexto na sessão se não estiver carregado
        if (self::temContextoDefinido() && !self::getContextoDaSessao()) {
            self::carregarContextoNaSessao();
        }
        
        return null; // Tudo OK, pode prosseguir
    }
}
