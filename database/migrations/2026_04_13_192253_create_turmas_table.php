<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('turmas', function (Blueprint $table) {
        $table->id();
        $table->string('modalidade'); // ex: EF - Ensino Fundamental
        $table->string('turno'); // ex: Matutino, Vespertino, Noturno, Integral
        $table->string('serie'); // ex: 1, 2, 3
        $table->string('complemento')->nullable(); // ex: COM (Nullable porque pode ficar em branco)
        $table->boolean('ativa')->default(true); // Atende ao requisito de opção de desativar turma
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turmas');
    }
};
