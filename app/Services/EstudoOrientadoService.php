<?php

namespace App\Services;

use App\Models\EstudoOrientadoSolicitacao;
use App\Models\EstudoOrientadoAtendimento;
use App\Models\EstudoOrientadoEvolucao;
use App\Models\EstudoOrientadoPlanoAcao;
use App\Models\EstudoOrientadoHistorico;
use App\Notifications\EstudoOrientadoSolicitadaNotification;
use App\Notifications\EstudoOrientadoEncaminhadaNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EstudoOrientadoService
{
    /**
     * Registra o histórico de uma solicitação.
     */
    private function registrarHistorico($solicitacaoId, $userId, $acao, $descricao = null, $anteriores = null, $novos = null)
    {
        EstudoOrientadoHistorico::create([
            'solicitacao_id' => $solicitacaoId,
            'user_id' => $userId,
            'acao' => $acao,
            'descricao' => $descricao,
            'dados_anteriores' => $anteriores,
            'dados_novos' => $novos,
        ]);
    }

    /**
     * Cria uma nova solicitação de encaminhamento pedagógico (Estudo Orientado).
     */
    public function criarSolicitacao(array $data): EstudoOrientadoSolicitacao
    {
        return DB::transaction(function () use ($data) {
            $solicitacao = EstudoOrientadoSolicitacao::create([
                'aluno_id'                 => $data['aluno_id'],
                'turma_id'                 => $data['turma_id'],
                'professor_solicitante_id' => $data['professor_solicitante_id'],
                'disciplina_solicitante'   => $data['disciplina_solicitante'],
                'motivo'                   => $data['motivo'],
                'prioridade'               => $data['prioridade'],
                'status'                   => 'Pendente',
            ]);

            $this->registrarHistorico(
                $solicitacao->id,
                $data['professor_solicitante_id'],
                'criou_solicitacao',
                'Solicitação criada com prioridade ' . $data['prioridade']
            );

            // Notifica coordenadores
            $coordenadores = User::role(User::TIPO_COORDENADOR)->get();
            foreach ($coordenadores as $coord) {
                $coord->notify(new EstudoOrientadoSolicitadaNotification($solicitacao));
            }

            return $solicitacao;
        });
    }

    /**
     * Coordenador aprova a solicitação (ainda sem orientador).
     */
    public function aprovarSolicitacao(int $solicitacaoId, int $coordenadorId, string $parecer): void
    {
        DB::transaction(function () use ($solicitacaoId, $coordenadorId, $parecer) {
            $solicitacao = EstudoOrientadoSolicitacao::findOrFail($solicitacaoId);
            $statusAnterior = $solicitacao->status;

            $solicitacao->update([
                'status' => 'Aprovada',
                'coordenador_id' => $coordenadorId,
                'data_analise' => now(),
                'parecer_coordenador' => $parecer,
            ]);

            $this->registrarHistorico(
                $solicitacaoId,
                $coordenadorId,
                'aprovou_solicitacao',
                'Solicitação aprovada. Parecer: ' . $parecer,
                ['status' => $statusAnterior],
                ['status' => 'Aprovada']
            );
        });
    }

    /**
     * Coordenador rejeita a solicitação.
     */
    public function rejeitarSolicitacao(int $solicitacaoId, int $coordenadorId, string $parecer): void
    {
        DB::transaction(function () use ($solicitacaoId, $coordenadorId, $parecer) {
            $solicitacao = EstudoOrientadoSolicitacao::findOrFail($solicitacaoId);
            $statusAnterior = $solicitacao->status;

            $solicitacao->update([
                'status' => 'Rejeitada',
                'coordenador_id' => $coordenadorId,
                'data_analise' => now(),
                'parecer_coordenador' => $parecer,
                'data_conclusao' => now(), // Encerra o fluxo
                'parecer_conclusao' => 'Rejeitada na análise inicial.',
                'concluido_por_id' => $coordenadorId,
            ]);

            $this->registrarHistorico(
                $solicitacaoId,
                $coordenadorId,
                'rejeitou_solicitacao',
                'Solicitação rejeitada. Parecer: ' . $parecer,
                ['status' => $statusAnterior],
                ['status' => 'Rejeitada']
            );
        });
    }

    /**
     * Coordenador atribui um orientador à solicitação aprovada.
     */
    public function atribuirOrientador(int $solicitacaoId, int $coordenadorId, int $orientadorId): void
    {
        DB::transaction(function () use ($solicitacaoId, $coordenadorId, $orientadorId) {
            $solicitacao = EstudoOrientadoSolicitacao::findOrFail($solicitacaoId);
            $orientadorAnterior = $solicitacao->professor_orientador_id;
            $statusAnterior = $solicitacao->status;

            $solicitacao->update([
                'status' => 'EmAtendimento',
                'professor_orientador_id' => $orientadorId,
                'data_atribuicao' => now(),
                'coordenador_id' => $coordenadorId, // Caso atribua junto com a aprovação
                'data_analise' => $solicitacao->data_analise ?? now(),
            ]);

            $this->registrarHistorico(
                $solicitacaoId,
                $coordenadorId,
                'atribuiu_orientador',
                'Orientador atribuído.',
                ['orientador_id' => $orientadorAnterior, 'status' => $statusAnterior],
                ['orientador_id' => $orientadorId, 'status' => 'EmAtendimento']
            );

            // Notifica orientador
            $orientador = User::find($orientadorId);
            if ($orientador) {
                $orientador->notify(new EstudoOrientadoEncaminhadaNotification($solicitacao));
            }
        });
    }

    /**
     * Registra um novo atendimento (sessão) do aluno.
     */
    public function registrarAtendimento(int $solicitacaoId, int $orientadorId, array $data): EstudoOrientadoAtendimento
    {
        return DB::transaction(function () use ($solicitacaoId, $orientadorId, $data) {
            $atendimento = EstudoOrientadoAtendimento::create([
                'solicitacao_id' => $solicitacaoId,
                'professor_orientador_id' => $orientadorId,
                'data_atendimento' => $data['data_atendimento'],
                'descricao' => $data['descricao'],
                'observacoes' => $data['observacoes'] ?? null,
            ]);

            $this->registrarHistorico(
                $solicitacaoId,
                $orientadorId,
                'registrou_atendimento',
                'Atendimento registrado em ' . $data['data_atendimento']
            );

            return $atendimento;
        });
    }

    /**
     * Registra um marco de evolução do aluno.
     */
    public function registrarEvolucao(int $solicitacaoId, int $orientadorId, array $data): EstudoOrientadoEvolucao
    {
        return DB::transaction(function () use ($solicitacaoId, $orientadorId, $data) {
            $evolucao = EstudoOrientadoEvolucao::create([
                'solicitacao_id' => $solicitacaoId,
                'professor_orientador_id' => $orientadorId,
                'data_registro' => $data['data_registro'],
                'descricao' => $data['descricao'],
                'indicador' => $data['indicador'],
            ]);

            $this->registrarHistorico(
                $solicitacaoId,
                $orientadorId,
                'registrou_evolucao',
                'Evolução registrada. Indicador: ' . $data['indicador']
            );

            return $evolucao;
        });
    }

    /**
     * Salva (cria ou atualiza) um plano de ação para a solicitação.
     */
    public function salvarPlanoAcao(int $solicitacaoId, int $orientadorId, array $data): EstudoOrientadoPlanoAcao
    {
        return DB::transaction(function () use ($solicitacaoId, $orientadorId, $data) {
            $plano = EstudoOrientadoPlanoAcao::create([
                'solicitacao_id' => $solicitacaoId,
                'professor_orientador_id' => $orientadorId,
                'descricao' => $data['descricao'],
                'metas' => $data['metas'] ?? null,
                'estrategias' => $data['estrategias'] ?? null,
                'prazo' => $data['prazo'] ?? null,
                'status' => 'Ativo',
            ]);

            $this->registrarHistorico(
                $solicitacaoId,
                $orientadorId,
                'criou_plano_acao',
                'Plano de ação criado.'
            );

            return $plano;
        });
    }

    /**
     * Finaliza o ciclo de acompanhamento do aluno.
     */
    public function concluirAcompanhamento(int $solicitacaoId, int $userId, string $parecer): void
    {
        DB::transaction(function () use ($solicitacaoId, $userId, $parecer) {
            $solicitacao = EstudoOrientadoSolicitacao::findOrFail($solicitacaoId);
            $statusAnterior = $solicitacao->status;

            $solicitacao->update([
                'status' => 'Concluida',
                'data_conclusao' => now(),
                'parecer_conclusao' => $parecer,
                'concluido_por_id' => $userId,
            ]);

            $this->registrarHistorico(
                $solicitacaoId,
                $userId,
                'concluiu_acompanhamento',
                'Acompanhamento concluído. Parecer: ' . $parecer,
                ['status' => $statusAnterior],
                ['status' => 'Concluida']
            );
        });
    }
}
