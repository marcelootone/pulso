<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enturmacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->string('tipo_vinculo')->default('REGULAR'); // REGULAR, ELETIVA, REFORCO, AEE, DEPENDENCIA, ITINERARIO
            $table->date('data_entrada')->nullable();
            $table->date('data_saida')->nullable();
            $table->string('status')->default('Ativo'); // Ativo, Transferido, Concluido, Cancelado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enturmacoes');
    }
};
