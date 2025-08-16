<?php

namespace App\Http\Controllers\Web\Administracao\Usuarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    /**
     * Exibe a interface do gerenciamento de usuários
     */
    public function index()
    {
        return view('administracao.usuarios.index');
    }
}
