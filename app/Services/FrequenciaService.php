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

            // Total de "presenças" para fins de frequência: apenas as FALTAS
            // INJUSTIFICADAS (status 'F') reduzem a frequência, conforme as diretrizes
            // da SEDU-ES. Presente (P) e Falta Justificada (FJ) contam como frequência,
            // padronizando o cálculo com o DashboardService e a Busca Ativa.
            $totalPresencas = Frequencia::where('turma_id', $turma->id)
                ->whereMonth('data', $mes)
                ->whereYear('data', $ano)
                ->whereIn('status', ['P', 'FJ'])
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

    public function getFrequenciasEletivaMonitoramento(int $eletivaId, string $data): array
    {
        $eletiva = \App\Models\Eletiva::with('alunos')->findOrFail($eletivaId);
        $alunos = $eletiva->alunos->sortBy('nome')->values();

        $frequencias = \App\Models\FrequenciaEletiva::with('professor')
            ->where('eletiva_id', $eletivaId)
            ->where('data', $data)
            ->get();

        if ($frequencias->isEmpty()) {
            return [];
        }

        $disciplinas = [];
        foreach ($frequencias->groupBy('user_id') as $professorId => $freqsProfessor) {
            $nomeDisciplina = $eletiva->nome;
            
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

    public function salvarFrequenciaEletiva(array $data, int $userId): void
    {
        $eletivaId = $data['eletiva_id'];
        $dataFrequencia = $data['data'];
        $frequencias = $data['frequencias'];

        DB::transaction(function () use ($eletivaId, $dataFrequencia, $frequencias, $userId) {
            foreach ($frequencias as $alunoId => $status) {
                \App\Models\FrequenciaEletiva::updateOrCreate(
                    [
                        'eletiva_id' => $eletivaId,
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

    public function getBuscaAtiva($mes, $ano, ?int $turmaId = null): Collection
    {
        $currentMonth = (int)date('n');
        $currentYear = (int)date('Y');
        
        $isMesTodos = $mes === 'todos';
        $isAnoTodos = $ano === 'todos';

        if (($mes == $currentMonth && $ano == $currentYear) || ($isMesTodos && $isAnoTodos)) {
            $dataReferencia = Carbon::now();
        } elseif ($isMesTodos) {
            $dataReferencia = Carbon::create($ano, 12, 31)->endOfDay();
            if ($ano == $currentYear) {
                $dataReferencia = Carbon::now();
            }
        } elseif ($isAnoTodos) {
            $dataReferencia = Carbon::create($currentYear, $mes)->endOfMonth();
            if ($mes == $currentMonth) {
                $dataReferencia = Carbon::now();
            }
        } else {
            $dataReferencia = Carbon::create($ano, $mes)->endOfMonth();
        }
        
        $queryRegistros = Frequencia::query()
            ->select(['aluno_id', 'turma_id', 'data', 'status'])
            ->where('data', '<=', $dataReferencia);

        if (!$isAnoTodos) {
            $inicioAno = Carbon::create((int) $ano, 1, 1)->startOfDay();
            $queryRegistros->where('data', '>=', $inicioAno);
        }

        if ($turmaId) {
            $queryRegistros->where('turma_id', $turmaId);
        }

        $frequencias = $queryRegistros->get();

        $alunosRisco = collect();
        $frequenciasPorAluno = $frequencias->groupBy('aluno_id');

        foreach ($frequenciasPorAluno as $alunoId => $freqsAluno) {
            // Janelas de tempo deslizantes (sliding windows) retroativas a partir da
            // data de referência, conforme as Diretrizes da SEDU-ES 2026 (Quadro 4):
            // semanal (7 dias), mensal (30 dias) e trimestral (90 dias), além do ano
            // completo. As comparações usam datas normalizadas (Y-m-d) para funcionar
            // independentemente de o campo 'data' vir como string ou Carbon.
            $lim7   = $dataReferencia->copy()->subDays(7)->format('Y-m-d');
            $lim30  = $dataReferencia->copy()->subDays(30)->format('Y-m-d');
            $lim90  = $dataReferencia->copy()->subDays(90)->format('Y-m-d');

            $emJanela = function ($collection, $limite) {
                return $collection->filter(function ($f) use ($limite) {
                    return Carbon::parse($f->data)->format('Y-m-d') >= $limite;
                });
            };

            $freqsAnual = $freqsAluno;
            $freqs90d = $emJanela($freqsAluno, $lim90);
            $freqsMes = $emJanela($freqsAluno, $lim30); // janela mensal = últimos 30 dias
            $freqs7d  = $emJanela($freqsAluno, $lim7);

            // Para cada janela: total de aulas, faltas injustificadas (status 'F'),
            // dias letivos com falta (datas distintas) e percentual de infrequência.
            $calc = function ($collection) {
                $total = $collection->count();
                $faltasCol = $collection->where('status', 'F');
                $faltas = $faltasCol->count();
                $diasFaltosos = $faltasCol
                    ->map(fn ($f) => Carbon::parse($f->data)->format('Y-m-d'))
                    ->unique()
                    ->count();
                $percentual = $total > 0 ? ($faltas / $total) * 100 : 0;
                return (object) ['total' => $total, 'faltas' => $faltas, 'dias_faltosos' => $diasFaltosos, 'percentual' => $percentual];
            };

            $anual = $calc($freqsAnual);
            $t90d = $calc($freqs90d);
            $tMes = $calc($freqsMes);
            $t7d  = $calc($freqs7d);

            // limiares da SEDU-ES 2026
            $motivos = [];
            
            if ($t7d->dias_faltosos >= 2 || $t7d->percentual >= 40) {
                $motivos[] = 'Semanal (>= 2 dias ou >= 40%)';
            }
            if ($tMes->dias_faltosos >= 5 || $tMes->percentual >= 25) {
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

                // Turma atual do estudante (enturmação ativa). Fallback: turma do filtro
                // ou a do registro de frequência mais recente. Evita mostrar turma errada
                // quando o aluno teve frequência em mais de uma turma no período.
                if ($turmaId) {
                    $turma = Turma::find($turmaId);
                } else {
                    $enturmacao = \App\Models\Enturmacao::where('status', 'Ativo')
                        ->whereHas('matricula', function ($q) use ($alunoId) {
                            $q->where('aluno_id', $alunoId);
                        })
                        ->with('turma')
                        ->latest('id')
                        ->first();

                    $turma = $enturmacao?->turma
                        ?? Turma::find($freqsAluno->sortByDesc('data')->first()->turma_id);
                }

                // Histórico de contatos do ano letivo de referência. A infrequência é
                // anual; restringir ao mês esconderia ações de acompanhamento anteriores.
                $registros = BuscaAtivaRegistro::where('aluno_id', $aluno->id)
                    ->when(!$isAnoTodos, function ($q) use ($ano) {
                        return $q->whereYear('data', (int) $ano);
                    })
                    ->with('user')
                    ->orderBy('data', 'desc')
                    ->get();

                $alunosRisco->push((object)[
                    'aluno' => $aluno,
                    'turma' => $turma,
                    'motivos' => implode(' | ', $motivos),
                    'percentual' => round($tMes->percentual, 1),
                    'total_faltas' => $tMes->faltas,
                    'registros' => $registros,
                    'anual_percentual' => round($anual->percentual, 1)
                ]);
            }
        }

        return $alunosRisco->sortByDesc('anual_percentual')->values();
    }
}
