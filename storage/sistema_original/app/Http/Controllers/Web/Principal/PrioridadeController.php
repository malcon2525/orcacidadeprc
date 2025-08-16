<?php

namespace App\Http\Controllers\Web\Principal;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;




class PrioridadeController extends Controller
{
    public function create(Request $request)
    {
        return view('principal.prioridade.cadastro');
    }
    public function fluxo(Request $request)
    {
        return view('principal.prioridade.fluxo');
    }
    public function documentacao(Request $request)
    {
        return view('principal.prioridade.documentacao');
    }
    public function historico(Request $request)
    {
        return view('principal.prioridade.historico');
    }

}