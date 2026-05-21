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
        Schema::create('aluno_turma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate data
        \Illuminate\Support\Facades\DB::statement('INSERT INTO aluno_turma (aluno_id, turma_id, created_at, updated_at) SELECT id, turma_id, NOW(), NOW() FROM alunos WHERE turma_id IS NOT NULL');

        // Drop the old column
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropForeign(['turma_id']);
            $table->dropColumn('turma_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->foreignId('turma_id')->nullable()->constrained('turmas')->onDelete('cascade');
        });
        Schema::dropIfExists('aluno_turma');
    }
};
