<?php

namespace App\Http\Controllers\Web\Administracao\ActiveDirectory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActiveDirectoryController extends Controller
{
    /**
     * Exibe a página principal do módulo Active Directory
     */
    public function index()
    {
        return view('administracao.active-directory.index');
    }
}