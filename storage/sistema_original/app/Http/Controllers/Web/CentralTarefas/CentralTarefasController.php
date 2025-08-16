<?php

namespace App\Http\Controllers\Web\CentralTarefas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;




class CentralTarefasController extends Controller
{
    public function inicio(Request $request)
    {
        return view('central_tarefas.inicio');
    }
   

}