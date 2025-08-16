<?php

namespace App\Http\Controllers\Web\Principal;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;




class PrincipalController extends Controller
{
    public function inicio(Request $request)
    {
        return view('principal.inicio');
       
    }
}