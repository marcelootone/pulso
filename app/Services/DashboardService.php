<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Frequencia;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Recupera os dados do dashboard para o Gestor (dados globais)
     */
    public function getDadosGestor()
    {
        return [
            'totalAlunos' => Aluno::count(),
            'totalTurmas' => Turma::where('ativa', true)->count(),
            'mediaEscola' => $this->calcularMediaEscola(),
            'alunosEmRisco' => $this->getAlunosEmRiscoTodos()->take(5)
        ];
    }

    /**
     * Recupera os dados do dashboard para o Professor (escopo restrito)
     */
    public function getDadosProfessor($user)
    {
        $turmasIds = $user->turmas()->where('ativa', true)->pluck('turmas.id');

        if ($turmasIds->isEmpty()) {
            return [
                'totalAlunos' => 0,
                'totalTurmas' => 0,
                'mediaEscola' => 0,
                'alunosEmRisco' => collect()
            ];
        }

        return [
            'totalAlunos' => DB::table('enturmacoes')
                ->join('matriculas', 'enturmacoes.matricula_id', '=', 'matriculas.id')
                ->whereIn('enturmacoes.turma_id', $turmasIds)
                ->where('enturmacoes.status', 'Ativo')
                ->distinct('matriculas.aluno_id')
                ->count('matriculas.aluno_id'),
            'totalTurmas' => $turmasIds->count(),
            'mediaEscola' => $this->calcularMediaEscola($turmasIds->toArray()),
            'alunosEmRisco' => $this->getAlunosEmRiscoTodos($turmasIds->toArray())->take(5)
        ];
    }

    /**
     * Calcula a média de frequência geral ou filtrada por turmas
     */
    private function calcularMediaEscola(array $turmasIds = [])
    {
        $query = Frequencia::query();
        if (!empty($turmasIds)) {
            $query->whereIn('turma_id', $turmasIds);
        }

        $totalRegistros = $query->count();
        $presencas = (clone $query)->whereIn('status', ['P', 'FJ'])->count();

        return $totalRegistros > 0 ? ($presencas / $totalRegistros) * 100 : 0;
    }

    /**
     * Retorna a Collection de alunos em risco (frequência < 75%)
     * Usado tanto no Dashboard quanto no Relatório de Evasão.
     */
    public function getAlunosEmRiscoTodos(array $turmasIds = [])
    {
        $query = Aluno::select('alunos.nome', 'alunos.ra', 'turmas.serie', 'turmas.complemento')
            ->join('matriculas', 'alunos.id', '=', 'matriculas.aluno_id')
            ->join('enturmacoes', 'matriculas.id', '=', 'enturmacoes.matricula_id')
            ->join('turmas', 'enturmacoes.turma_id', '=', 'turmas.id')
            ->join('frequencias', function ($join) {
                // Junta apenas as frequências que pertencem à mesma turma em que o aluno está sendo avaliado
                $join->on('alunos.id', '=', 'frequencias.aluno_id')
                     ->on('turmas.id', '=', 'frequencias.turma_id');
            })
            ->where('enturmacoes.status', 'Ativo')
            ->selectRaw('COUNT(CASE WHEN frequencias.status IN ("P", "FJ") THEN 1 END) * 100 / COUNT(frequencias.id) as percentual')
            ->groupBy('alunos.id', 'alunos.nome', 'alunos.ra', 'turmas.serie', 'turmas.complemento')
            ->having('percentual', '<', 75)
            ->orderBy('percentual', 'asc');

        if (!empty($turmasIds)) {
            $query->whereIn('turmas.id', $turmasIds);
        }

        return $query->get();
    }
}
