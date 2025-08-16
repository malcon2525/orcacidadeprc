<?php

namespace App\Http\Controllers\Web\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiveDirectoryController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a interface principal do Active Directory
     * O Vue.js fará todo o gerenciamento através da API
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.active-directory.index');
        }
        
        // 2. Tem permissão específica?
        if ($user->hasPermission('gerenciar_active_directory')) {
            return view('administracao.active-directory.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Exibe a interface de configurações do Active Directory
     */
    public function config()
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.active-directory.config');
        }
        
        // 2. Tem permissão específica?
        if ($user->hasPermission('gerenciar_active_directory')) {
            return view('administracao.active-directory.config');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Exibe a interface de sincronização do Active Directory
     */
    public function sync()
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.active-directory.sync');
        }
        
        // 2. Tem permissão específica?
        if ($user->hasPermission('gerenciar_active_directory')) {
            return view('administracao.active-directory.sync');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
