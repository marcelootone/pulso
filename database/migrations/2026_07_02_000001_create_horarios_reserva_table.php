<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RF11 - Configurar e gerenciar horários para a reserva de espaços.
 * Faixas de horário (períodos/aulas) que ficam disponíveis para seleção
 * ao reservar um espaço, conforme pré-condição do caso de uso UC24.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_reserva', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();   // Ex.: "1ª Aula", "Turno Matutino"
            $table->time('horario_inicio');
            $table->time('horario_fim');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['horario_inicio', 'horario_fim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_reserva');
    }
};
