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
        // First add the user_id column. It should probably be nullable initially to avoid constraint errors on existing data.
        Schema::table('conteudo_ministrados', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        });

        Schema::table('conteudo_ministrados', function (Blueprint $table) {
            $table->unique(['turma_id', 'data', 'aula_numero', 'user_id'], 'conteudo_novo_unique');
            $table->dropUnique(['turma_id', 'data', 'aula_numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conteudo_ministrados', function (Blueprint $table) {
            $table->dropUnique(['turma_id', 'data', 'aula_numero', 'user_id']);
            $table->unique(['turma_id', 'data', 'aula_numero']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
