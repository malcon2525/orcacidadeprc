<?php

namespace App\Http\Controllers\Web\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('tabela_oficial.consultar_sinapi.index');
    }
}
