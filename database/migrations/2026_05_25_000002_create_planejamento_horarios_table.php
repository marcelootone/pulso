<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planejamento_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planejamento_id')->constrained('planejamento_semanal')->cascadeOnDelete();
            $table->unsignedInteger('ordem');
            $table->string('horario_inicio', 5); // HH:MM
            $table->string('horario_fim', 5);     // HH:MM
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planejamento_horarios');
    }
};
