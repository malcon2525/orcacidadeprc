<?php

namespace App\Http\Controllers\Web\Administracao\Municipios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    /**
     * Exibe a interface do CRUD
     */
    public function index()
    {
        return view('administracao.municipios.index');
    }
}
