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
        Schema::create('estudo_orientado_solicitacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos');          // POR ALUNO (não por turma)
            $table->foreignId('turma_id')->constrained('turmas');           // Turma do aluno no momento
            $table->foreignId('professor_solicitante_id')->constrained('users');
            $table->string('disciplina_solicitante', 100);
            $table->text('motivo');                                         // Motivo do encaminhamento
            $table->enum('prioridade', ['Baixa', 'Media', 'Alta'])->default('Media');
            $table->enum('status', [
                'Pendente',         // Criada pelo professor
                'Aprovada',         // Aprovada pelo coordenador
                'Rejeitada',        // Rejeitada pelo coordenador
                'EmAtendimento',    // Orientador iniciou acompanhamento
                'Concluida',        // Acompanhamento finalizado
            ])->default('Pendente');
            
            // Análise do Coordenador
            $table->foreignId('coordenador_id')->nullable()->constrained('users');
            $table->timestamp('data_analise')->nullable();
            $table->text('parecer_coordenador')->nullable();
            
            // Atribuição do Orientador
            $table->foreignId('professor_orientador_id')->nullable()->constrained('users');
            $table->timestamp('data_atribuicao')->nullable();
            
            // Conclusão
            $table->timestamp('data_conclusao')->nullable();
            $table->text('parecer_conclusao')->nullable();
            $table->foreignId('concluido_por_id')->nullable()->constrained('users');
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['aluno_id', 'status']);
            $table->index(['professor_solicitante_id']);
            $table->index(['professor_orientador_id', 'status'], 'idx_eo_solic_prof_orient_status');
            $table->index(['coordenador_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudo_orientado_solicitacoes');
    }
};
