<?php

namespace App\Services;

use App\Models\Turma;
use App\Models\Aluno;
use App\Models\Frequencia;
use App\Models\BuscaAtivaRegistro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class FrequenciaService
{
    /**
     * Retorna o resumo de frequência de todas as turmas ativas para um mês/ano.
     */
    public function getResumoTurmas(int $mes, int $ano): Collection
    {
        $turmas = Turma::where('ativa', true)
            ->orderBy('modalidade')
            ->orderBy('serie')
            ->orderBy('complemento')
            ->get();

        foreach ($turmas as $turma) {
            // Total de registros de frequência para a turma neste mês
            $totalRegistros = Frequencia::where('turma_id', $turma->id)
                ->whereMonth('data', $mes)
                ->whereYear('data', $ano)
                ->count();

            // Total de presenças
            $totalPresencas = Frequencia::where('turma_id', $turma->id)
                ->whereMonth('data', $mes)
                ->whereYear('data', $ano)
                ->where('status', 'P')
                ->count();

            $turma->percentual_frequencia = $totalRegistros > 0 
                ? round(($totalPresencas / $totalRegistros) * 100, 1) 
                : 0;
            
            $turma->dias_letivos_registrados = Frequencia::where('turma_id', $turma->id)
                ->whereMonth('data', $mes)
                ->whereYear('data', $ano)
                ->distinct('data')
                ->count('data');
        }

        return $turmas;
    }

    /**
     * Retorna a lista de alunos ativos de uma turma, com seus status de frequência para a data.
     */
    public function getAlunosTurma(int $turmaId, string $data): Collection
    {
        // Busca os alunos ativos na turma
        $turma = Turma::with(['enturmacoes' => function ($q) {
            $q->where('status', 'Ativo')->with('matricula.aluno');
        }])->findOrFail($turmaId);

        $alunos = $turma->enturmacoes->map(function ($enturmacao) {
            return $enturmacao->matricula->aluno;
        })->sortBy('nome')->values();

        // Mapear frequencias existentes na data
        $frequencias = Frequencia::with('user')
            ->where('turma_id', $turmaId)
            ->where('data', $data)
            ->get()
            ->groupBy('aluno_id');

        foreach ($alunos as $aluno) {
            $aluno->frequencias_dia = $frequencias->get($aluno->id, collect());
            $aluno->frequencia_lancada = $aluno->frequencias_dia->isNotEmpty();
            
            $aluno->status_frequencia = 'P';
            if (auth()->check()) {
                $minhaFreq = $aluno->frequencias_dia->where('user_id', auth()->id())->first();
                if ($minhaFreq) {
                    $aluno->status_frequencia = $minhaFreq->status;
                }
            }
        }

        return $alunos;
    }

    /**
     * Salva a chamada para uma turma em uma data.
     * $data deve conter: ['turma_id' => X, 'data' => Y, 'frequencias' => [aluno_id => status]]
     */
    public function salvarFrequencia(array $data, int $userId): void
    {
        $turmaId = $data['turma_id'];
        $dataFrequencia = $data['data'];
        $frequencias = $data['frequencias'];

        DB::transaction(function () use ($turmaId, $dataFrequencia, $frequencias, $userId) {
            foreach ($frequencias as $alunoId => $status) {
                Frequencia::updateOrCreate(
                    [
                        'turma_id' => $turmaId,
                        'aluno_id' => $alunoId,
                        'data' => $dataFrequencia,
                        'user_id' => $userId
                    ],
                    [
                        'status' => $status
                    ]
                );
            }
        });
    }

    /**
     * Retorna os alunos com frequência abaixo de 75% no mês selecionado.
     */
    public function getBuscaAtiva(int $mes, int $ano, ?int $turmaId = null): Collection
    {
        $queryRegistros = Frequencia::whereMonth('data', $mes)
            ->whereYear('data', $ano);

        if ($turmaId) {
            $queryRegistros->where('turma_id', $turmaId);
        }

        // Agrupa por aluno
        $dadosFrequencia = $queryRegistros->selectRaw('aluno_id, turma_id, count(*) as total, sum(case when status = "P" then 1 else 0 end) as presencas, sum(case when status = "F" then 1 else 0 end) as faltas')
            ->groupBy('aluno_id', 'turma_id')
            ->get();

        $alunosRisco = collect();

        foreach ($dadosFrequencia as $dado) {
            $percentual = $dado->total > 0 ? ($dado->presencas / $dado->total) * 100 : 0;
            
            if ($percentual < 75) {
                $aluno = Aluno::find($dado->aluno_id);
                $turma = Turma::find($dado->turma_id);
                
                // Buscar registros da Busca Ativa já feitos neste mês para este aluno
                $registros = BuscaAtivaRegistro::where('aluno_id', $aluno->id)
                    ->whereMonth('data', $mes)
                    ->whereYear('data', $ano)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $alunosRisco->push((object)[
                    'aluno' => $aluno,
                    'turma' => $turma,
                    'percentual' => round($percentual, 1),
                    'total_faltas' => $dado->faltas,
                    'registros' => $registros
                ]);
            }
        }

        return $alunosRisco->sortBy('percentual')->values();
    }
}
