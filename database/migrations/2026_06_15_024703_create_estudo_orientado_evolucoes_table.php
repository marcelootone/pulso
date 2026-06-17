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
        Schema::create('estudo_orientado_evolucoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('estudo_orientado_solicitacoes');
            $table->foreignId('professor_orientador_id')->constrained('users');
            $table->date('data_registro');
            $table->text('descricao');
            $table->enum('indicador', ['Melhora', 'Estavel', 'Piora']);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['solicitacao_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudo_orientado_evolucoes');
    }
};
