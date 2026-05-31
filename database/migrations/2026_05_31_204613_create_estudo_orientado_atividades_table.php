<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudo_orientado_atividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('professor_solicitante_id')->constrained('users')->cascadeOnDelete();
            $table->string('disciplina_solicitante', 100);
            $table->date('data_prevista');
            $table->text('descricao');
            $table->enum('status', ['Pendente', 'Avaliada'])->default('Pendente');
            $table->timestamps();

            $table->index(['turma_id', 'status']);
            $table->index('professor_solicitante_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudo_orientado_atividades');
    }
};
