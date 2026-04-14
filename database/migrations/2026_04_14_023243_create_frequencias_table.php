<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('frequencias', function (Blueprint $table) {
        $table->id();
        
        // Quem levou a falta/presença?
        $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
        // Em qual turma?
        $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
        // Qual professor registrou?
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->date('data'); // Data da aula
        $table->enum('status', ['P', 'F', 'FJ'])->default('P'); // Presente, Falta, Falta Justificada
        
        $table->timestamps();
        
        // Regra de Negócio: Um aluno não pode ter dois registros de presença na mesma turma, no mesmo dia
        $table->unique(['aluno_id', 'turma_id', 'data']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequencias');
    }
};
