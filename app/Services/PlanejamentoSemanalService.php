<?php

namespace App\Services;

use App\Models\PlanejamentoHorario;
use App\Models\PlanejamentoItem;
use App\Models\PlanejamentoSemanal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PlanejamentoSemanalService
{
    /**
     * Dias da semana utilizados no planejamento.
     */
    public const DIAS_SEMANA = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA'];

    /**
     * Horários padrão: matutino + vespertino (aulas de 50min, intervalo de 20min).
     */
    private const HORARIOS_PADRAO = [
        // Matutino
        ['inicio' => '07:00', 'fim' => '07:50'],
        ['inicio' => '07:50', 'fim' => '08:40'],
        ['inicio' => '08:40', 'fim' => '09:30'],
        ['inicio' => '09:30', 'fim' => '09:50'], // Intervalo matutino
        ['inicio' => '09:50', 'fim' => '10:40'],
        ['inicio' => '10:40', 'fim' => '11:30'],
        // Vespertino
        ['inicio' => '13:00', 'fim' => '13:50'],
        ['inicio' => '13:50', 'fim' => '14:40'],
        ['inicio' => '14:40', 'fim' => '15:30'],
        ['inicio' => '15:30', 'fim' => '15:50'], // Intervalo vespertino
        ['inicio' => '15:50', 'fim' => '16:40'],
        ['inicio' => '16:40', 'fim' => '17:30'],
    ];

    /**
     * Calcula segunda e sexta da semana contendo a data informada.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function calcularSemana(Carbon $data): array
    {
        $segunda = $data->copy()->startOfWeek(Carbon::MONDAY);
        $sexta = $segunda->copy()->addDays(4);

        return [$segunda, $sexta];
    }

    /**
     * Obtém ou cria o planejamento da semana para o usuário.
     */
    public function obterOuCriarSemana(User $user, Carbon $data): PlanejamentoSemanal
    {
        [$segunda, $sexta] = $this->calcularSemana($data);

        $planejamento = PlanejamentoSemanal::where('user_id', $user->id)
            ->where('semana_inicio', $segunda->toDateString())
            ->first();

        if (!$planejamento) {
            $planejamento = DB::transaction(function () use ($user, $segunda, $sexta) {
                $plan = PlanejamentoSemanal::create([
                    'user_id' => $user->id,
                    'semana_inicio' => $segunda,
                    'semana_fim' => $sexta,
                ]);

                $this->gerarHorariosPadrao($plan);

                return $plan;
            });
        }

        // Carrega horários com itens eager-loaded
        $planejamento->load('horarios.itens');

        return $planejamento;
    }

    /**
     * Gera os horários padrão (matutino + vespertino) com itens vazios para cada dia.
     */
    public function gerarHorariosPadrao(PlanejamentoSemanal $planejamento): void
    {
        foreach (self::HORARIOS_PADRAO as $ordem => $slot) {
            $horario = PlanejamentoHorario::create([
                'planejamento_id' => $planejamento->id,
                'ordem' => $ordem + 1,
                'horario_inicio' => $slot['inicio'],
                'horario_fim' => $slot['fim'],
            ]);

            $this->criarItensVazios($horario);
        }
    }

    /**
     * Cria itens vazios para os 5 dias da semana em um horário.
     */
    private function criarItensVazios(PlanejamentoHorario $horario): void
    {
        foreach (self::DIAS_SEMANA as $dia) {
            PlanejamentoItem::create([
                'horario_id' => $horario->id,
                'dia_semana' => $dia,
                'tarefa' => null,
                'andamento' => null,
                'observacao' => null,
            ]);
        }
    }

    /**
     * Salva todas as alterações do formulário (horários + itens).
     */
    public function salvarAlteracoes(array $dados, PlanejamentoSemanal $planejamento): void
    {
        DB::transaction(function () use ($dados, $planejamento) {
            $horariosData = $dados['horarios'] ?? [];

            foreach ($horariosData as $horarioId => $horarioData) {
                $horario = PlanejamentoHorario::where('id', $horarioId)
                    ->where('planejamento_id', $planejamento->id)
                    ->first();

                if (!$horario) {
                    continue;
                }

                // Atualiza horário início/fim
                $horario->update([
                    'horario_inicio' => $horarioData['horario_inicio'] ?? $horario->horario_inicio,
                    'horario_fim' => $horarioData['horario_fim'] ?? $horario->horario_fim,
                ]);

                // Atualiza itens de cada dia
                $itensData = $horarioData['itens'] ?? [];
                foreach ($itensData as $dia => $itemData) {
                    if (!in_array($dia, self::DIAS_SEMANA)) {
                        continue;
                    }

                    PlanejamentoItem::updateOrCreate(
                        [
                            'horario_id' => $horario->id,
                            'dia_semana' => $dia,
                        ],
                        [
                            'tarefa' => $itemData['tarefa'] ?? null,
                            'andamento' => !empty($itemData['andamento']) ? $itemData['andamento'] : null,
                            'observacao' => $itemData['observacao'] ?? null,
                        ]
                    );
                }
            }
        });
    }

    /**
     * Adiciona um novo horário ao planejamento com itens vazios.
     */
    public function adicionarHorario(
        PlanejamentoSemanal $planejamento,
        string $horarioInicio,
        string $horarioFim
    ): PlanejamentoHorario {
        $ultimaOrdem = PlanejamentoHorario::where('planejamento_id', $planejamento->id)
            ->max('ordem') ?? 0;

        $horario = PlanejamentoHorario::create([
            'planejamento_id' => $planejamento->id,
            'ordem' => $ultimaOrdem + 1,
            'horario_inicio' => $horarioInicio,
            'horario_fim' => $horarioFim,
        ]);

        $this->criarItensVazios($horario);

        return $horario;
    }

    /**
     * Remove um horário (e seus itens, via cascade) após validar ownership.
     */
    public function removerHorario(int $horarioId, int $userId): bool
    {
        $horario = PlanejamentoHorario::whereHas('planejamento', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->find($horarioId);

        if (!$horario) {
            return false;
        }

        $horario->delete();

        return true;
    }

    /**
     * Reordena os horários baseando-se em um array de IDs.
     */
    public function reordenarHorarios(array $horariosIds, int $planejamentoId, int $userId): void
    {
        $planejamento = PlanejamentoSemanal::where('id', $planejamentoId)
            ->where('user_id', $userId)
            ->first();

        if (!$planejamento) {
            return;
        }

        DB::transaction(function () use ($horariosIds, $planejamento) {
            foreach ($horariosIds as $index => $horarioId) {
                PlanejamentoHorario::where('id', $horarioId)
                    ->where('planejamento_id', $planejamento->id)
                    ->update(['ordem' => $index + 1]);
            }
        });
    }
}
