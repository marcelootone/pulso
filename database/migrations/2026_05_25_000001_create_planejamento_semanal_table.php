<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planejamento_semanal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('semana_inicio');
            $table->date('semana_fim');
            $table->timestamps();

            $table->unique(['user_id', 'semana_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planejamento_semanal');
    }
};
