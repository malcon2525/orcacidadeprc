<?php

namespace App\Http\Controllers\Web\Perfil\MeuPerfil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeuPerfilController extends Controller
{
    /**
     * Exibe a página do perfil do usuário
     */
    public function index()
    {
        // 1. Verificação de autenticação
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Retorna a view com container Vue
        return view('perfil.meu-perfil.index', [
            'permissoes' => [
                'consultar' => true,
                'editar_dados' => true,
                'alterar_senha' => true
            ]
        ]);
    }
}
