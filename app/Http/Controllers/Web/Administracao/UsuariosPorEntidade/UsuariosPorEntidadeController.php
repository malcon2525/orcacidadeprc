<?php

namespace App\Http\Controllers\Web\Administracao\UsuariosPorEntidade;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UsuariosPorEntidadeController extends Controller
{
    /**
     * Exibe a página de usuários por entidade
     */
    public function index(): View
    {
        // Verificação de acesso
        $this->checkAccess(['gerenciar_usuarios']);
        
        return view('administracao.usuarios-por-entidade.index');
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