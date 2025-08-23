<?php

namespace App\Http\Controllers\Web\TabelaOficial\ConsultarDerpr;

use App\Http\Controllers\Controller;
use App\Models\Administracao\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsável por gerenciar a interface de consulta da tabela DER-PR
 * 
 * Este controller fornece endpoints para:
 * - Exibir a interface de consulta
 * - Gerenciar a navegação e interação do usuário
 */
class ConsultarDerprController extends Controller
{
    /**
     * Exibe a página inicial de consulta
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Verifica se é super admin ou tem papel específico para consultar DER-PR
        if ($user->isSuperAdmin() || $user->hasRole('consultar_tabela_derpr')) {
            $permissoes = [
                'crud' => $user->isSuperAdmin(),
                'consultar' => true, // Sempre true para quem tem acesso
                'importar' => false  // Não há importação nesta funcionalidade
            ];
        } else {
            abort(403, 'Acesso negado. Papel insuficiente.');
        }

        return view('tabela_oficial.consultar_derpr.index', compact('permissoes'));
    }
}
