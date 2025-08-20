<?php

namespace App\Http\Controllers\Web\Administracao\EstruturaOrcamento;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstruturaOrcamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a interface da funcionalidade
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Verifica se é super admin ou tem papel específico
        if ($user->isSuperAdmin() || $user->hasRole('gerenciar_estrutura_orcamento')) {
            $permissoes = [
                'crud' => $user->isSuperAdmin() || $user->hasPermission('estrutura_orcamento_crud'),
                'consultar' => $user->isSuperAdmin() || $user->hasPermission('estrutura_orcamento_consultar'),
                'importar' => $user->isSuperAdmin() || $user->hasPermission('estrutura_orcamento_importar')
            ];
        } else {
            abort(403, 'Acesso negado');
        }

        return view('administracao.estrutura-orcamento.index', compact('permissoes'));
    }
}
