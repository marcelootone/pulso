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
        Schema::dropIfExists('frequencia_eletivas');
        Schema::create('frequencia_eletivas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->foreignId('eletiva_id')->constrained('eletivas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('data');
            $table->enum('status', ['P', 'F', 'FJ']);
            $table->timestamps();
            
            $table->unique(['aluno_id', 'eletiva_id', 'data'], 'freq_eletiva_aluno_data_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequencia_eletivas');
    }
};
