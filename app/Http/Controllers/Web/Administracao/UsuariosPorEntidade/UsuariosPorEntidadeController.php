<?php

namespace App\Http\Controllers\Web\Administracao\UsuariosPorEntidade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuariosPorEntidadeController extends Controller
{
    /**
     * Exibe a interface de gerenciamento de usuários por entidade
     */
    public function index()
    {
        // Verificação de acesso conforme padrões
        /** @var \App\Models\Administracao\User $user */
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.usuarios-por-entidade.index');
        }
        
        // 2. Tem permissão específica? → Acesso ao módulo
        if ($user->hasPermission('aprovar-cadastros')) {
            return view('administracao.usuarios-por-entidade.index');
        }
        
        // 3. Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente para gerenciar usuários por entidade.');
    }

    /**
     * Verificação de acesso unificada
     */
    private function checkAccess($permissions, $requireAll = false)
    {
        /** @var \App\Models\Administracao\User $user */
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
}
