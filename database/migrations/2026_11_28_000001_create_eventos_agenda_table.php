<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_agenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('local')->nullable();
            $table->dateTime('inicio');
            $table->dateTime('fim');
            $table->boolean('dia_inteiro')->default(false);
            $table->boolean('compartilhado')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'inicio']);
            $table->index(['compartilhado', 'inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_agenda');
    }
};
