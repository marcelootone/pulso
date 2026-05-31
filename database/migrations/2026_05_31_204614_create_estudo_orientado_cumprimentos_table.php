<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudo_orientado_cumprimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atividade_id')->constrained('estudo_orientado_atividades')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->boolean('cumpriu')->default(false);
            $table->string('observacao', 500)->nullable();
            $table->timestamps();

            $table->unique(['atividade_id', 'aluno_id']);
            $table->index('aluno_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudo_orientado_cumprimentos');
    }
};
