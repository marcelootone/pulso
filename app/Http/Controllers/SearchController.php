<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];
        $user = Auth::user();

        // 1. Buscar Estudantes
        if ($user->can('ver estudantes') || $user->can('gerenciar estudantes')) {
            $alunos = Aluno::where('nome', 'like', "%{$query}%")
                ->orWhere('ra', 'like', "%{$query}%")
                ->take(5)
                ->get()
                ->map(function ($aluno) {
                    return [
                        'id' => $aluno->id,
                        'title' => $aluno->nome,
                        'subtitle' => 'RA: ' . $aluno->ra,
                        'url' => route('alunos.show', $aluno->id),
                        'type' => 'Estudante',
                        'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' // Academic Cap
                    ];
                });

            if ($alunos->count() > 0) {
                $results['Estudantes'] = $alunos;
            }
        }

        // 2. Buscar Turmas
        if ($user->can('acessar turmas vinculadas') || $user->can('gerenciar turmas')) {
            $turmas = Turma::where('nome', 'like', "%{$query}%")
                ->orWhere('ano_letivo', 'like', "%{$query}%")
                ->take(5)
                ->get()
                ->map(function ($turma) {
                    return [
                        'id' => $turma->id,
                        'title' => $turma->nome,
                        'subtitle' => $turma->ano_letivo . ' - ' . ucfirst($turma->turno),
                        'url' => route('turmas.show', $turma->id),
                        'type' => 'Turma',
                        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' // User Group
                    ];
                });
                
            if ($turmas->count() > 0) {
                $results['Turmas'] = $turmas;
            }
        }

        // 3. Buscar Funcionários
        if ($user->can('gerenciar professores')) {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('tipo_usuario', 'like', "%{$query}%")
                ->take(5)
                ->get()
                ->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'title' => $u->name,
                        'subtitle' => $u->tipo_usuario ?? 'Usuário',
                        'url' => route('users.show', $u->id),
                        'type' => 'Funcionário',
                        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' // User
                    ];
                });

            if ($users->count() > 0) {
                $results['Funcionários'] = $users;
            }
        }

        // 4. Buscar Espaços
        $espacos = \App\Models\Espaco::where('nome', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'title' => $e->nome,
                    'subtitle' => 'Capacidade: ' . $e->capacidade,
                    'url' => route('agendamentos.create', $e->id),
                    'type' => 'Espaço',
                    'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
                ];
            });

        if ($espacos->count() > 0) {
            $results['Espaços'] = $espacos;
        }

        return response()->json(['results' => $results]);
    }
}
