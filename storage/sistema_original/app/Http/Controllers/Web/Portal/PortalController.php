<?php

namespace App\Http\Controllers\Web\Portal;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


// ********************  NÃO ESTAMOS UTILIZANDO NESSE MOMENTO.     *************************
//FOI CRIADO PARA TER FUNÇÕES RELACIONADAS COM O PORTAL DOS MUNICÍPIOS



class PortalController extends Controller
{
    public function listaUsuarios(Request $request)
    {
        // Retornar resposta 204 (No Content) 
        return response()->noContent();      
    }
}
