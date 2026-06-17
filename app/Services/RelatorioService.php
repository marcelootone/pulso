<?php

namespace App\Services;

use App\Models\Turma;
use App\Models\Frequencia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RelatorioService
{
    /**
     * Retorna a frequência mensal detalhada de uma turma específica.
     */
    public function frequenciaMensal(int $turmaId, int $mes, int $ano): array
    {
        $turma = Turma::with(['enturmacoes' => function ($q) {
            $q->where('status', 'Ativo')->with('matricula.aluno');
        }])->findOrFail($turmaId);

        $alunos = $turma->enturmacoes->map(function ($enturmacao) {
            return $enturmacao->matricula->aluno;
        })->sortBy('nome')->values();

        // Extrai todos os dias do mês em que houve chamada para esta turma
        $diasComChamada = Frequencia::with('user:id,name')
            ->where('turma_id', $turmaId)
            ->whereMonth('data', $mes)
            ->whereYear('data', $ano)
            ->select('data', 'user_id')
            ->distinct()
            ->orderBy('data')
            ->get()
            ->map(function ($item) {
                return [
                    'data' => $item->data,
                    'user_id' => $item->user_id,
                    'professor_nome' => explode(' ', trim($item->user->name ?? 'Prof'))[0]
                ];
            });

        // Busca todas as frequências da turma no mês indexadas por [aluno_id][data_userid]
        $frequenciasBrutas = Frequencia::where('turma_id', $turmaId)
            ->whereMonth('data', $mes)
            ->whereYear('data', $ano)
            ->get();

        $mapaFrequencias = [];
        foreach ($frequenciasBrutas as $freq) {
            $key = $freq->data . '_' . $freq->user_id;
            $mapaFrequencias[$freq->aluno_id][$key] = $freq->status;
        }

        // Monta o resumo por aluno
        $resumoAlunos = [];
        foreach ($alunos as $aluno) {
            $dias = [];
            $totalP = 0;
            $totalF = 0;
            $totalFJ = 0;

            foreach ($diasComChamada as $sessao) {
                $key = $sessao['data'] . '_' . $sessao['user_id'];
                $status = $mapaFrequencias[$aluno->id][$key] ?? '-';
                $dias[$key] = $status;

                if ($status === 'P') $totalP++;
                if ($status === 'F') $totalF++;
                if ($status === 'FJ') $totalFJ++;
            }

            $total = $totalP + $totalF + $totalFJ;
            $percentual = $total > 0 ? (($totalP + $totalFJ) / $total) * 100 : 0;

            $resumoAlunos[] = [
                'aluno' => $aluno,
                'dias' => $dias,
                'total_presencas' => $totalP,
                'total_faltas' => $totalF,
                'total_faltas_justificadas' => $totalFJ,
                'percentual' => round($percentual, 1)
            ];
        }

        return [
            'turma' => $turma,
            'dias_letivos' => $diasComChamada,
            'alunos' => $resumoAlunos
        ];
    }

    /**
     * Retorna o ranking das turmas com os maiores percentuais de ausência no mês.
     */
    public function rankingTurmasFaltas(int $mes, int $ano): Collection
    {
        $dadosFrequencia = Frequencia::whereMonth('data', $mes)
            ->whereYear('data', $ano)
            ->selectRaw('turma_id, count(*) as total, sum(case when status = "F" then 1 else 0 end) as faltas')
            ->groupBy('turma_id')
            ->get();

        $ranking = collect();

        foreach ($dadosFrequencia as $dado) {
            if ($dado->total > 0) {
                $turma = Turma::find($dado->turma_id);
                $percentualFaltas = ($dado->faltas / $dado->total) * 100;

                $ranking->push((object)[
                    'turma' => $turma,
                    'total_registros' => $dado->total,
                    'total_faltas' => $dado->faltas,
                    'percentual_ausencia' => round($percentualFaltas, 1)
                ]);
            }
        }

        // Ordena de forma decrescente (turmas que mais faltam aparecem primeiro)
        return $ranking->sortByDesc('percentual_ausencia')->values();
    }
}
