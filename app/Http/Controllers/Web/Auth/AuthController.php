<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Exibe a página de login
     */
    public function showLoginForm()
    {
        // Se já estiver autenticado, redireciona para home
        if (Auth::check()) {
            return redirect()->route('home');
        }
        
        return view('auth.login');
    }
}
