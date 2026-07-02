<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alinha o registro de Busca Ativa ao DER/Modelo de Classes do TCC: o registro
 * vincula-se à matrícula do estudante monitorado. Mantém-se aluno_id por
 * compatibilidade; matricula_id é preenchido com a matrícula ativa no momento
 * do registro.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('busca_ativa_registros', function (Blueprint $table) {
            $table->foreignId('matricula_id')
                ->nullable()
                ->after('aluno_id')
                ->constrained('matriculas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('busca_ativa_registros', function (Blueprint $table) {
            $table->dropForeign(['matricula_id']);
            $table->dropColumn('matricula_id');
        });
    }
};
