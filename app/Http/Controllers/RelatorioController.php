<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // <-- A MÁGICA DO PDF VEM DAQUI

class RelatorioController extends Controller
{
    public function evasao()
    {
        // 1. Busca os alunos com menos de 75% de frequência (A mesma lógica do Dashboard)
        $alunosEmRisco = Aluno::select('alunos.nome', 'alunos.ra', 'turmas.serie', 'turmas.complemento')
            ->join('turmas', 'alunos.turma_id', '=', 'turmas.id')
            ->join('frequencias', 'alunos.id', '=', 'frequencias.aluno_id')
            ->selectRaw('COUNT(CASE WHEN frequencias.status = "P" THEN 1 END) * 100 / COUNT(frequencias.id) as percentual')
            ->groupBy('alunos.id', 'alunos.nome', 'alunos.ra', 'turmas.serie', 'turmas.complemento')
            ->having('percentual', '<', 75)
            ->orderBy('percentual', 'asc')
            ->get();

        $dataAtual = date('d/m/Y');

        // 2. Carrega uma View HTML e injeta os dados nela
        $pdf = Pdf::loadView('relatorios.evasao', compact('alunosEmRisco', 'dataAtual'));

        // 3. Força o download do ficheiro com um nome profissional
        return $pdf->download('alerta_evasao_sigae_' . date('Y_m_d') . '.pdf');
    }
}