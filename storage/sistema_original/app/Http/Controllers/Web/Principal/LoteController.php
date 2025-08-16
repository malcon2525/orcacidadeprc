<?php

namespace App\Http\Controllers\Web\Principal;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;




class LoteController extends Controller
{

    //lote
    public function municipio(Request $request)
    {
        return view('principal.lote.municipio');
    }
    public function prc(Request $request)
    {
        return view('principal.lote.prc');
    }
    public function analise(Request $request)
    {
        return view('principal.lote.analise');
    }
    public function conclusao(Request $request)
    {
        return view('principal.lote.conclusao');
    }
    








    public function orcamento(Request $request)
    {
        return view('principal.lote.orcamento');
    }
   

}