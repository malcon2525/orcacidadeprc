<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        Log::info('=== TENTATIVA DE LOGIN ===', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        // Validação simples
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'Digite um email válido',
            'password.required' => 'O campo senha é obrigatório'
        ]);

        // Tentar login
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Log::info('Login realizado com sucesso', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email
            ]);

            return redirect()->intended($this->redirectTo);
        }

        Log::warning('Falha no login', ['email' => $request->email]);

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Credenciais inválidas.']);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Log::info('=== LOGOUT ===', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'N/A'
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Logout realizado com sucesso');

        return redirect('/')->with('status', 'Logout realizado com sucesso!');
    }
}