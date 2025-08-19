<?php

namespace App\Http\Controllers\Web\Administracao\Usuarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Sistema unificado de verificação de permissões
     * 
     * @param string|array $permissions Permissões necessárias
     * @param bool $requireAll Se true, todas as permissões são obrigatórias (AND)
     * @return bool
     */
    private function checkAccess($permissions, $requireAll = false)
    {
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // 2. Verificação flexível de permissões
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }
        
        if ($requireAll) {
            // Todas as permissões são obrigatórias (AND)
            foreach ($permissions as $permission) {
                if (!$user->hasPermission($permission)) {
                    abort(403, "Permissão obrigatória: {$permission}");
                }
            }
        } else {
            // Pelo menos uma permissão é suficiente (OR)
            $hasAnyPermission = false;
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasAnyPermission = true;
                    break;
                }
            }
            
            if (!$hasAnyPermission) {
                abort(403, 'Acesso negado. Permissão insuficiente.');
            }
        }
        
        return true;
    }

    /**
     * Exibe a interface principal do gerenciamento de usuários
     * O Vue.js fará todo o CRUD através da API
     */
    public function index()
    {
        // MENU: verifica se tem papel gerenciar_usuarios (acesso ao menu)
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.usuarios.index');
        }
        
        // 2. Tem o papel gerenciar_usuarios? → Acesso ao módulo
        if ($user->hasRole('gerenciar_usuarios')) {
            return view('administracao.usuarios.index');
        }
        
        // 3. Tem o papel visualizar_usuarios? → Acesso ao módulo (apenas consulta)
        if ($user->hasRole('visualizar_usuarios')) {
            return view('administracao.usuarios.index');
        }
        
        // 3. Acesso negado
        abort(403, 'Acesso negado. Papel insuficiente.');
    }
}
