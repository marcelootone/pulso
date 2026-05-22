<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Frequencia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPIs Básicos
        $totalAlunos = Aluno::count();
        $totalTurmas = Turma::where('ativa', true)->count();

        // 2. Cálculo de Frequência Global da Escola
        $totalRegistros = Frequencia::count();
        $presencas = Frequencia::where('status', 'P')->count();
        $mediaEscola = $totalRegistros > 0 ? ($presencas / $totalRegistros) * 100 : 0;

        // 3. Alunos em Risco (Frequência < 75%)
        // Agrupamos por aluno, contamos presenças e calculamos a média
        $alunosEmRisco = Aluno::select('alunos.nome', 'alunos.ra', 'turmas.serie', 'turmas.complemento')
            ->join('matriculas', 'alunos.id', '=', 'matriculas.aluno_id')
            ->join('enturmacoes', 'matriculas.id', '=', 'enturmacoes.matricula_id')
            ->join('turmas', 'enturmacoes.turma_id', '=', 'turmas.id')
            ->join('frequencias', 'alunos.id', '=', 'frequencias.aluno_id')
            ->selectRaw('COUNT(CASE WHEN frequencias.status = "P" THEN 1 END) * 100 / COUNT(frequencias.id) as percentual')
            ->groupBy('alunos.id', 'alunos.nome', 'alunos.ra', 'turmas.serie', 'turmas.complemento')
            ->having('percentual', '<', 75)
            ->orderBy('percentual', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact('totalAlunos', 'totalTurmas', 'mediaEscola', 'alunosEmRisco'));
    }
}