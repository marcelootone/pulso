<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('alunos', function (Blueprint $table) {
        $table->id();
        
        // Relacionamento: este aluno pertence a uma turma
        $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
        
        // Dados da Planilha
        $table->string('ra')->unique(); // ID (RA) não pode repetir
        $table->string('nome');
        $table->string('nascimento')->nullable(); 
        $table->string('sexo', 1)->nullable(); // M ou F
        $table->string('telefone')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
