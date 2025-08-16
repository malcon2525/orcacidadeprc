<?php

namespace App\Http\Controllers\Web\TabelaOficial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImportarDerprController extends Controller
{
    /**
     * Exibe a interface de importação DER-PR
     */
    public function index()
    {
        return view('tabela_oficial.importar_derpr.index');
    }
} 