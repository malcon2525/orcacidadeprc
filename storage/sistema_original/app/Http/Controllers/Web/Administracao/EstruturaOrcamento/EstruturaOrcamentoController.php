<?php

namespace App\Http\Controllers\Web\Administracao\EstruturaOrcamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstruturaOrcamentoController extends Controller
{
    /**
     * Exibe a interface da funcionalidade
     */
    public function index()
    {
        return view('administracao.estrutura-orcamento.index');
    }
}
