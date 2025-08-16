<?php

namespace App\Http\Controllers\Web\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('web.tabela_oficial.consultar_derpr.index');
    }
}
