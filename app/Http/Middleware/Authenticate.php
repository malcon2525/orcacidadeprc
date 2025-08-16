<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Se for uma requisição API, retorna null (sem redirecionamento)
        if ($request->expectsJson()) {
            return null;
        }
        
        // Se for uma requisição web, redireciona para login
        return route('login');
    }
}
