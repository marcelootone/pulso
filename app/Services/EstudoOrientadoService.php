<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\EstudoOrientadoAtividade;
use App\Models\EstudoOrientadoCumprimento;
use App\Models\Enturmacao;
use Illuminate\Support\Facades\DB;

class EstudoOrientadoService
{
    /**
     * Cria uma nova solicitação de atividade de Estudo Orientado.
     *
     * @param array $data
     * @return EstudoOrientadoAtividade
     */
    public function criarSolicitacao(array $data): EstudoOrientadoAtividade
    {
        return DB::transaction(function () use ($data) {
            return EstudoOrientadoAtividade::create([
                'turma_id'                 => $data['turma_id'],
                'professor_solicitante_id' => $data['professor_solicitante_id'],
                'disciplina_solicitante'   => $data['disciplina_solicitante'],
                'data_prevista'            => $data['data_prevista'],
                'descricao'               => $data['descricao'],
                'status'                  => 'Pendente',
            ]);
        });
    }

    /**
     * Salva o resultado da avaliação do Professor de Estudo Orientado.
     * Usa updateOrCreate para ser idempotente (pode ser re-enviado sem duplicar).
     * Ao final, marca a atividade como 'Avaliada'.
     *
     * @param int   $atividadeId
     * @param array $cumprimentos  [aluno_id => ['cumpriu' => bool, 'observacao' => string|null], ...]
     * @return void
     */
    public function salvarAvaliacao(int $atividadeId, array $cumprimentos): void
    {
        DB::transaction(function () use ($atividadeId, $cumprimentos) {
            $atividade = EstudoOrientadoAtividade::findOrFail($atividadeId);

            foreach ($cumprimentos as $alunoId => $dados) {
                EstudoOrientadoCumprimento::updateOrCreate(
                    [
                        'atividade_id' => $atividadeId,
                        'aluno_id'     => $alunoId,
                    ],
                    [
                        'cumpriu'    => (bool) ($dados['cumpriu'] ?? false),
                        'observacao' => $dados['observacao'] ?? null,
                    ]
                );
            }

            // Marca a atividade como avaliada
            $atividade->update(['status' => 'Avaliada']);
        });
    }

    /**
     * Retorna os alunos ativos de uma turma, ordenados por nome.
     * Usa a cadeia Enturmacao -> Matricula -> Aluno.
     *
     * @param int $turmaId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function alunosDaTurma(int $turmaId)
    {
        $alunoIds = Enturmacao::where('turma_id', $turmaId)
            ->where('status', 'Ativo')
            ->pluck('matricula_id');

        // Percorre matriculas para chegar nos alunos
        $alunos = Aluno::whereHas('matriculas', function ($q) use ($alunoIds) {
            $q->whereIn('id', $alunoIds);
        })->orderBy('nome')->get();

        return $alunos;
    }
}
