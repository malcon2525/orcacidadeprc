<?php

namespace App\Http\Controllers\Web\Principal;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;




class ProjetoController extends Controller
{
    public function create(Request $request)
    {
        return view('principal.projeto.cadastro');
    }
    public function fluxo(Request $request)
    {
        return view('principal.projeto.fluxo');
    }
    public function cotacao(Request $request)
    {
        return view('principal.projeto.cotacao');
    }
    public function cotacaoCAD(Request $request)
    {
        return view('principal.projeto.cotacao_cadastro');
    }
    public function composicao(Request $request)
    {
        return view('principal.projeto.composicao');
    }
    public function composicaoCAD(Request $request)
    {
        return view('principal.projeto.composicao_cadastro');
    }
   

}