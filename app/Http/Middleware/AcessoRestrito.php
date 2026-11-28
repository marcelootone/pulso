<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AcessoRestrito
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se o usuário não estiver logado, manda pro login
        if (!Auth::check()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
