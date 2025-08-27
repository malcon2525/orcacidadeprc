<?php

namespace App\Http\Controllers\Web\Orcamento\ComposicaoPropria;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComposicaoPropriaController extends Controller
{
    /**
     * Verifica se o usuário tem acesso à funcionalidade
     */
    private function checkAccess()
    {
        $user = User::find(Auth::id());
        
        // Super admin tem acesso total
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // Verifica permissões específicas
        if (!$user->hasPermission('gerenciar_composicoes_proprias') && 
            !$user->hasPermission('visualizar_composicoes_proprias')) {
            abort(403, 'Acesso negado. Permissão insuficiente.');
        }
        
        return true;
    }

    /**
     * Exibe a página principal de composições próprias
     */
    public function index()
    {
        $this->checkAccess();
        
        // Determina as permissões do usuário
        $user = User::find(Auth::id());
        $permissoes = [
            'crud' => $user->isSuperAdmin() || $user->hasPermission('gerenciar_composicoes_proprias'),
            'consultar' => $user->isSuperAdmin() || $user->hasPermission('visualizar_composicoes_proprias'),
        ];
        
        return view('orcamento.composicao-propria.index', compact('permissoes'));
    }
}
