<?php

namespace App\Http\Controllers\Web\Transporte;

use App\Http\Controllers\Controller;

class CustoTransportePageController extends Controller
{
    public function index()
    {
        return view('transporte.custos');
    }
} 