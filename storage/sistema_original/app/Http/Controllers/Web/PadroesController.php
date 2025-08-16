<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PadroesController extends Controller
{
    /**
     * Exibe a página de padrões de interface
     */
    public function index()
    {
        return view('padroes.index');
    }
}
