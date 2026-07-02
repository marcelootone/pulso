<?php

namespace Database\Seeders;

use App\Models\HorarioReserva;
use Illuminate\Database\Seeder;

/**
 * RF11 - Faixas de horário padrão para a reserva de espaços.
 */
class HorariosReservaSeeder extends Seeder
{
    public function run(): void
    {
        $faixas = [
            ['nome' => '1ª Aula', 'horario_inicio' => '07:00', 'horario_fim' => '07:50'],
            ['nome' => '2ª Aula', 'horario_inicio' => '07:50', 'horario_fim' => '08:40'],
            ['nome' => '3ª Aula', 'horario_inicio' => '08:40', 'horario_fim' => '09:30'],
            ['nome' => '4ª Aula', 'horario_inicio' => '09:50', 'horario_fim' => '10:40'],
            ['nome' => '5ª Aula', 'horario_inicio' => '10:40', 'horario_fim' => '11:30'],
        ];

        foreach ($faixas as $faixa) {
            HorarioReserva::firstOrCreate(
                ['horario_inicio' => $faixa['horario_inicio'], 'horario_fim' => $faixa['horario_fim']],
                ['nome' => $faixa['nome'], 'ativo' => true]
            );
        }
    }
}
