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

        // Se o usuário for um 'Estudante', bloqueia o acesso e manda pro painel geral
        if (Auth::user()->tipo_usuario === \App\Models\User::TIPO_ESTUDANTE) {
            // Mais para frente, criaremos a rota '/painel-aluno'
            return redirect('/dashboard')->with('error', 'Acesso restrito. Área exclusiva para funcionários.');
        }

        // Se não for estudante (ou seja, Secretaria, Professor, etc), deixa passar!
        return $next($request);
    }
}