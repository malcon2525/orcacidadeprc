<?php

namespace App\Http\Controllers\Web\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controller para gerenciar a interface web de importação SINAPI
 * 
 * Responsável por servir as views da funcionalidade de importação SINAPI
 * dentro do módulo Tabela Oficial.
 */
class ImportarSinapiController extends Controller
{
    /**
     * Exibe a página principal de importação SINAPI
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('tabela_oficial.importar_sinapi.index');
    }
} 