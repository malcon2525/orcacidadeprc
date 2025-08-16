<?php

namespace App\Http\Controllers\Web\Principal;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;




class OrcamentoController extends Controller
{
    public function orcamentoInicio(Request $request)
    {
        return view('principal.orcamento.inicio');
    }
    public function orcamentoBdi(Request $request)
    {
        return view('principal.orcamento.bdi');
    }
    public function orcamentoDmt(Request $request)
    {
        return view('principal.orcamento.dmt');
    }
    public function orcamentoPlanilha(Request $request)
    {
        return view('principal.orcamento.planilha');
    }
    public function orcamentoResumo(Request $request)
    {
        return view('principal.orcamento.resumo');
    }
   

}