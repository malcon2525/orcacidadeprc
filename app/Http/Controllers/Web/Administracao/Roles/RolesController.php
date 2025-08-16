<?php

namespace App\Http\Controllers\Web\Administracao\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolesController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a interface principal do gerenciamento de papéis
     * O Vue.js fará todo o CRUD através da API
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.roles.index');
        }
        
        // 2. Tem permissão específica?
        if ($user->hasPermission('gerenciar_papeis')) {
            return view('administracao.roles.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
