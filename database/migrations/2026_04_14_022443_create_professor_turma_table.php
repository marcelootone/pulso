<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('professor_turma', function (Blueprint $table) {
        $table->id();
        
        // ID do Professor (que vem da tabela users)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        // ID da Turma
        $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
        
        // Qual matéria ele ensina lá?
        $table->string('disciplina');
        
        $table->timestamps();
        
        // Impede que a Secretaria cadastre o mesmo professor na mesma turma com a mesma matéria duas vezes
        $table->unique(['user_id', 'turma_id', 'disciplina']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_turma');
    }
};
