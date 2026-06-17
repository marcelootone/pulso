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
        Schema::create('estudo_orientado_atendimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('estudo_orientado_solicitacoes');
            $table->foreignId('professor_orientador_id')->constrained('users');
            $table->date('data_atendimento');
            $table->text('descricao');               // O que foi trabalhado
            $table->text('observacoes')->nullable(); // Observações adicionais
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['solicitacao_id']);
            $table->index(['professor_orientador_id']);
            $table->index(['data_atendimento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudo_orientado_atendimentos');
    }
};
