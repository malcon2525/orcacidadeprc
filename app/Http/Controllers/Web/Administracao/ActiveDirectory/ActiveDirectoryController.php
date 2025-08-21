<?php

namespace App\Http\Controllers\Web\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use App\Services\Logging\ActiveDirectoryLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiveDirectoryController extends Controller
{
    protected $logger;

    /**
     * Construtor com middleware de autenticação
     */
    public function __construct(ActiveDirectoryLogService $logger)
    {
        $this->middleware('auth');
        $this->logger = $logger;
    }

    /**
     * Exibe a interface principal do Active Directory
     * O Vue.js fará todo o gerenciamento através da API
     */
    public function index()
    {
        $user = User::find(Auth::id());
        
        // Log de acesso à funcionalidade
        $this->logger->acessoFuncionalidade('PRINCIPAL', [
            'usuario_id' => $user->id,
            'usuario_email' => $user->email,
            'usuario_papeis' => $user->roles->pluck('name')->toArray()
        ]);
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            $this->logger->sucessoAcessoFuncionalidade('PRINCIPAL', [
                'tipo_acesso' => 'super_admin'
            ]);
            return view('administracao.active-directory.index');
        }
        
        // 2. Tem o papel gerenciar_ad? → Acesso ao módulo
        if ($user->hasRole('gerenciar_ad')) {
            $this->logger->sucessoAcessoFuncionalidade('PRINCIPAL', [
                'tipo_acesso' => 'papel_gerenciar_ad'
            ]);
            return view('administracao.active-directory.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        $this->logger->acessoNegadoFuncionalidade('PRINCIPAL', [
            'usuario_id' => $user->id,
            'papeis_usuario' => $user->roles->pluck('name')->toArray()
        ]);
        
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Exibe a interface de configurações do Active Directory
     */
    public function config()
    {
        $user = User::find(Auth::id());
        
        // Log de acesso à funcionalidade
        $this->logger->acessoFuncionalidade('CONFIGURACOES', [
            'usuario_id' => $user->id,
            'usuario_email' => $user->email,
            'usuario_papeis' => $user->roles->pluck('name')->toArray()
        ]);
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            $this->logger->sucessoAcessoFuncionalidade('CONFIGURACOES', [
                'tipo_acesso' => 'super_admin'
            ]);
            return view('administracao.active-directory.index');
        }
        
        // 2. Tem o papel gerenciar_ad? → Acesso ao módulo
        if ($user->hasRole('gerenciar_ad')) {
            $this->logger->sucessoAcessoFuncionalidade('CONFIGURACOES', [
                'tipo_acesso' => 'papel_gerenciar_ad'
            ]);
            return view('administracao.active-directory.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        $this->logger->acessoNegadoFuncionalidade('CONFIGURACOES', [
            'usuario_id' => $user->id,
            'papeis_usuario' => $user->roles->pluck('name')->toArray()
        ]);
        
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Exibe a interface de sincronização do Active Directory
     */
    public function sync()
    {
        $user = User::find(Auth::id());
        
        // Log de acesso à funcionalidade
        $this->logger->acessoFuncionalidade('SINCRONIZACAO', [
            'usuario_id' => $user->id,
            'usuario_email' => $user->email,
            'usuario_papeis' => $user->roles->pluck('name')->toArray()
        ]);
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            $this->logger->sucessoAcessoFuncionalidade('SINCRONIZACAO', [
                'tipo_acesso' => 'super_admin'
            ]);
            return view('administracao.active-directory.index');
        }
        
        // 2. Tem o papel gerenciar_ad? → Acesso ao módulo
        if ($user->hasRole('gerenciar_ad')) {
            $this->logger->sucessoAcessoFuncionalidade('SINCRONIZACAO', [
                'tipo_acesso' => 'papel_gerenciar_ad'
            ]);
            return view('administracao.active-directory.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        $this->logger->acessoNegadoFuncionalidade('SINCRONIZACAO', [
            'usuario_id' => $user->id,
            'papeis_usuario' => $user->roles->pluck('name')->toArray()
        ]);
        
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
