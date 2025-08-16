<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Se for uma requisição API, retorna erro
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Usuário já autenticado',
                        'redirect' => RouteServiceProvider::HOME
                    ], 400);
                }
                
                // Se for uma requisição web, redireciona para home
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
