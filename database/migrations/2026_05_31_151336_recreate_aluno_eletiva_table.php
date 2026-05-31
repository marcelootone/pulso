<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aluno_eletiva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->foreignId('eletiva_id')->constrained('eletivas')->onDelete('cascade');
            $table->date('data_inscricao');
            $table->date('data_saida')->nullable();
            $table->enum('status', ['Ativo', 'Transferido', 'Removido'])->default('Ativo');
            $table->timestamps();
            
            $table->unique(['aluno_id', 'eletiva_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aluno_eletiva');
    }
};
