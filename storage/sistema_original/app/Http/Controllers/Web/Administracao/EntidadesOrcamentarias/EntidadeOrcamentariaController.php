<?php

namespace App\Http\Controllers\Web\Administracao\EntidadesOrcamentarias;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntidadeOrcamentariaController extends Controller
{
    /**
     * Exibe a interface do CRUD
     */
    public function index()
    {
        return view('administracao.entidades-orcamentarias.index');
    }
}
