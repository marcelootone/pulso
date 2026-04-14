<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aluno_eletiva', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->foreignId('eletiva_id')->constrained('eletivas')->onDelete('cascade');
            
            $table->timestamps();
            
            // Impede que o mesmo aluno seja matriculado duas vezes na mesma eletiva
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
