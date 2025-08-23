<?php

namespace App\Http\Controllers\Web\TabelaOficial\ConsultarSinapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsável por gerenciar as consultas à tabela SINAPI
 * 
 * Este controller fornece endpoints para:
 * - Exibir a interface de consulta
 * - Listar as tabelas disponíveis (data_base e desoneracao)
 * - Buscar os dados específicos de uma tabela selecionada
 */
class ConsultarSinapiController extends Controller
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
        
        // Verifica se é super admin ou tem papel específico
        if ($user->isSuperAdmin() || $user->hasRole('gerenciar_tabela_oficial') || $user->hasRole('visualizar_tabela_oficial')) {
            $permissoes = [
                'crud' => $user->isSuperAdmin() || $user->hasPermission('tabela_oficial_crud'),
                'consultar' => $user->isSuperAdmin() || $user->hasPermission('tabela_oficial_consultar'),
                'importar' => $user->isSuperAdmin() || $user->hasPermission('tabela_oficial_importar')
            ];
        } else {
            abort(403, 'Acesso negado');
        }

        return view('tabela_oficial.consultar_sinapi.index', compact('permissoes'));
    }
}
