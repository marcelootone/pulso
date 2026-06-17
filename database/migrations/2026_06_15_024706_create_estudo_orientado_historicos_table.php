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
        Schema::create('estudo_orientado_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('estudo_orientado_solicitacoes');
            $table->foreignId('user_id')->constrained('users');
            $table->string('acao', 100);             // Ex: 'criou_solicitacao', 'aprovou', 'rejeitou', etc.
            $table->text('descricao')->nullable();
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->timestamps();
            
            $table->index(['solicitacao_id']);
            $table->index(['user_id']);
            $table->index(['acao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudo_orientado_historicos');
    }
};
