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
        Schema::table('frequencias', function (Blueprint $table) {
            // Add new unique constraint including user_id FIRST so the foreign key index is satisfied
            $table->unique(['aluno_id', 'turma_id', 'data', 'user_id'], 'frequencias_novo_unique');
            // Drop old unique constraint
            $table->dropUnique(['aluno_id', 'turma_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frequencias', function (Blueprint $table) {
            $table->dropUnique(['aluno_id', 'turma_id', 'data', 'user_id']);
            $table->unique(['aluno_id', 'turma_id', 'data']);
        });
    }
};
