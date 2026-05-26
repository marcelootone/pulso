<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planejamento_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_id')->constrained('planejamento_horarios')->cascadeOnDelete();
            $table->enum('dia_semana', ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA']);
            $table->string('tarefa')->nullable();
            $table->enum('andamento', ['CONCLUIDO', 'EM_ANDAMENTO', 'NAO_CONCLUIDO'])->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['horario_id', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planejamento_itens');
    }
};
