<?php

namespace App\Http\Controllers\Web\Administracao\Permissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionsController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a interface principal do gerenciamento de permissões
     * O Vue.js fará todo o CRUD através da API
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.permissions.index');
        }
        
        // 2. Tem permissão específica?
        if ($user->hasPermission('gerenciar_permissoes')) {
            return view('administracao.permissions.index');
        }
        
        // 3. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente.');
    }
}
