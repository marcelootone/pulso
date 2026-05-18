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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('email');
            $table->string('cpf')->nullable()->unique()->after('username');
            $table->date('nascimento')->nullable()->after('cpf');
            $table->string('sexo', 1)->nullable()->after('nascimento');
            $table->string('telefone')->nullable()->after('sexo');
            $table->string('cidade')->nullable()->after('telefone');
            $table->string('rua')->nullable()->after('cidade');
            $table->string('numero')->nullable()->after('rua');
            $table->string('bairro')->nullable()->after('numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'cpf', 'nascimento', 'sexo', 'telefone', 'cidade', 'rua', 'numero', 'bairro']);
        });
    }
};
