<?php

namespace App\Http\Controllers\Web\Administracao\UsuariosPorEntidade;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UsuariosPorEntidadeController extends Controller
{
    /**
     * Construtor com middleware de autenticação
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a interface de usuários por entidade
     * O Vue.js fará todo o gerenciamento através da API
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        
        // 1. É super admin? → Acesso total
        if ($user->isSuperAdmin()) {
            return view('administracao.usuarios-por-entidade.index', [
                'permissoes' => [
                    'crud' => true,
                    'consultar' => true,
                    'vincular' => true
                ]
            ]);
        }
        
        // 2. Tem permissão para gerenciar vínculos de usuários?
        if ($user->hasPermission('gerenciar_vinculos_usuarios')) {
            return view('administracao.usuarios-por-entidade.index', [
                'permissoes' => [
                    'crud' => true,
                    'consultar' => true,
                    'vincular' => true
                ]
            ]);
        }
        
        // 3. Tem permissão apenas para visualizar?
        if ($user->hasPermission('visualizar_cadastros_usuarios')) {
            return view('administracao.usuarios-por-entidade.index', [
                'permissoes' => [
                    'crud' => false,
                    'consultar' => true,
                    'vincular' => false
                ]
            ]);
        }
        
        // 4. Nenhuma das opções → Acesso negado
        abort(403, 'Acesso negado. Permissão insuficiente para gerenciar usuários por entidade.');
    }

}