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
     * Retorna a lista de disciplinas registradas para a turma na data, com os alunos e seus status.
     */
    public function getFrequenciasMonitoramento(int $turmaId, string $data): array
    {
        // 1. Obter alunos ativos da turma
        $turma = Turma::with(['enturmacoes' => function ($q) {
            $q->where('status', 'Ativo')->with('matricula.aluno');
        }])->findOrFail($turmaId);

        $alunos = $turma->enturmacoes->map(function ($enturmacao) {
            return $enturmacao->matricula->aluno;
        })->sortBy('nome')->values();

        // 2. Obter registros de frequência na data informada
        $frequencias = Frequencia::with('user')
            ->where('turma_id', $turmaId)
            ->where('data', $data)
            ->get();

        if ($frequencias->isEmpty()) {
            return []; // Nenhuma disciplina registrada
        }

        // 3. Obter as disciplinas dos professores que registraram frequência
        $professoresIds = $frequencias->pluck('user_id')->unique();
        $disciplinasProfessores = DB::table('professor_turma')
            ->where('turma_id', $turmaId)
            ->whereIn('user_id', $professoresIds)
            ->pluck('disciplina', 'user_id');

        // 4. Montar a estrutura por disciplina
        $disciplinas = [];
        foreach ($frequencias->groupBy('user_id') as $professorId => $freqsProfessor) {
            $nomeDisciplina = $disciplinasProfessores->get($professorId) ?? 'Geral/Coordenação';
            
            $freqsPorAluno = $freqsProfessor->keyBy('aluno_id');
            
            $alunosComStatus = $alunos->map(function($aluno) use ($freqsPorAluno) {
                $freq = $freqsPorAluno->get($aluno->id);
                return (object)[
                    'id' => $aluno->id,
                    'nome' => $aluno->nome,
                    'status_frequencia' => $freq ? $freq->status : null,
                ];
            });

            $disciplinas[] = (object)[
                'nome' => $nomeDisciplina,
                'professor_id' => $professorId,
                'alunos' => $alunosComStatus
            ];
        }

        return $disciplinas;
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

    public function getBuscaAtiva(int $mes, int $ano, ?int $turmaId = null): Collection
    {
        $currentMonth = (int)date('n');
        $currentYear = (int)date('Y');
        
        if ($mes === $currentMonth && $ano === $currentYear) {
            $dataReferencia = Carbon::now();
        } else {
            $dataReferencia = Carbon::create($ano, $mes)->endOfMonth();
        }

        $inicioAno = Carbon::create($ano, 1, 1)->startOfDay();
        
        $queryRegistros = Frequencia::where('data', '>=', $inicioAno)
            ->where('data', '<=', $dataReferencia);

        if ($turmaId) {
            $queryRegistros->where('turma_id', $turmaId);
        }

        $frequencias = $queryRegistros->get();

        $alunosRisco = collect();
        $frequenciasPorAluno = $frequencias->groupBy('aluno_id');

        foreach ($frequenciasPorAluno as $alunoId => $freqsAluno) {
            // janelas retroativas a partir da data de referência
            $freqsAnual = $freqsAluno;
            $freqs90d = $freqsAluno->where('data', '>=', $dataReferencia->copy()->subDays(90));
            $freqs30d = $freqsAluno->where('data', '>=', $dataReferencia->copy()->subDays(30));
            $freqs7d = $freqsAluno->where('data', '>=', $dataReferencia->copy()->subDays(7));
            
            // para cada janela: total, dias de falta (distintos) e percentual
            $calc = function($collection) {
                $total = $collection->count();
                $faltas = $collection->where('status', 'F')->count();
                // Faltas (dias letivos) = distinct datas with status F
                $diasFaltosos = $collection->where('status', 'F')->pluck('data')->unique()->count();
                $percentual = $total > 0 ? ($faltas / $total) * 100 : 0;
                return (object) ['total' => $total, 'faltas' => $faltas, 'dias_faltosos' => $diasFaltosos, 'percentual' => $percentual];
            };

            $anual = $calc($freqsAnual);
            $t90d = $calc($freqs90d);
            $t30d = $calc($freqs30d);
            $t7d = $calc($freqs7d);

            // limiares da SEDU-ES 2026
            $motivos = [];
            
            if ($t7d->dias_faltosos >= 2 || $t7d->percentual >= 40) {
                $motivos[] = 'Semanal (>= 2 dias ou >= 40%)';
            }
            if ($t30d->dias_faltosos >= 5 || $t30d->percentual >= 25) {
                $motivos[] = 'Mensal (>= 5 dias ou >= 25%)';
            }
            if ($t90d->dias_faltosos >= 12 || $t90d->percentual >= 20) {
                $motivos[] = 'Trimestral (>= 12 dias ou >= 20%)';
            }
            if ($anual->percentual > 25 && $anual->percentual <= 35) {
                $motivos[] = 'Anual Portaria 254-R (> 25% a <= 35%)';
            } elseif ($anual->percentual > 35) {
                $motivos[] = 'Anual (> 35% de faltas)';
            }

            if (!empty($motivos)) {
                $aluno = Aluno::find($alunoId);
                if (!$aluno) continue;

                $tId = $turmaId ?? $freqsAluno->first()->turma_id;
                $turma = Turma::find($tId);

                $registros = BuscaAtivaRegistro::where('aluno_id', $aluno->id)
                    ->whereMonth('data', $mes)
                    ->whereYear('data', $ano)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $alunosRisco->push((object)[
                    'aluno' => $aluno,
                    'turma' => $turma,
                    'motivos' => implode(' | ', $motivos),
                    'percentual' => round($t30d->percentual, 1), 
                    'total_faltas' => $t30d->faltas, 
                    'registros' => $registros,
                    'anual_percentual' => round($anual->percentual, 1)
                ]);
            }
        }

        return $alunosRisco->sortByDesc('anual_percentual')->values();
    }
}
