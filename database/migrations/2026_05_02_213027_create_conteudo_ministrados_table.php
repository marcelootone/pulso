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
        Schema::create('conteudo_ministrados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->string('disciplina')->nullable();
            $table->date('data');
            $table->string('aula_numero'); // Ex: '1', '2'
            $table->text('descricao')->nullable();
            $table->timestamps();

            $table->unique(['turma_id', 'data', 'aula_numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conteudo_ministrados');
    }
};
