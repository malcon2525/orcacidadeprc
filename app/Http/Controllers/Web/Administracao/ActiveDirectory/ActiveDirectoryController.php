<?php

namespace App\Http\Controllers\Web\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
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
        $user = User::find(Auth::id());
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.active-directory.index');
        }
        
        // 2. Tem o papel gerenciar_ad? → Acesso ao módulo
        if ($user->hasRole('gerenciar_ad')) {
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
        $user = User::find(Auth::id());
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.active-directory.index');
        }
        
        // 2. Tem o papel gerenciar_ad? → Acesso ao módulo
        if ($user->hasRole('gerenciar_ad')) {
            return view('administracao.active-directory.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }

    /**
     * Exibe a interface de sincronização do Active Directory
     */
    public function sync()
    {
        $user = User::find(Auth::id());
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.active-directory.index');
        }
        
        // 2. Tem o papel gerenciar_ad? → Acesso ao módulo
        if ($user->hasRole('gerenciar_ad')) {
            return view('administracao.active-directory.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
