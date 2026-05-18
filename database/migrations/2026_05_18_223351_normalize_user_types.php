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
        // Normaliza "PROFESSOR(A)", "Professor(a)", "Professora" para "Professor"
        DB::table('users')
            ->whereIn('tipo_usuario', ['PROFESSOR(A)', 'Professor(a)', 'Professora', 'PROFESSORA'])
            ->update(['tipo_usuario' => \App\Models\User::TIPO_PROFESSOR]);

        // Normaliza "ESTUDANTE", "ALUNO", "Aluno" para "Estudante"
        DB::table('users')
            ->whereIn('tipo_usuario', ['ESTUDANTE', 'ALUNO', 'Aluno'])
            ->update(['tipo_usuario' => \App\Models\User::TIPO_ESTUDANTE]);
            
        // Normaliza "GESTOR"
        DB::table('users')
            ->whereIn('tipo_usuario', ['GESTOR', 'Gestor'])
            ->update(['tipo_usuario' => \App\Models\User::TIPO_GESTOR]);
            
        // Normaliza "COORDENADOR"
        DB::table('users')
            ->whereIn('tipo_usuario', ['COORDENADOR', 'Coordenador'])
            ->update(['tipo_usuario' => \App\Models\User::TIPO_COORDENADOR]);
            
        // Normaliza "SECRETARIA"
        DB::table('users')
            ->whereIn('tipo_usuario', ['SECRETARIA', 'Secretaria'])
            ->update(['tipo_usuario' => \App\Models\User::TIPO_SECRETARIA]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter não é perfeitamente possível sem saber o valor original,
        // mas podemos desfazer a mudança principal de Professor se necessário.
        DB::table('users')
            ->where('tipo_usuario', \App\Models\User::TIPO_PROFESSOR)
            ->update(['tipo_usuario' => 'PROFESSOR(A)']);
            
        DB::table('users')
            ->where('tipo_usuario', \App\Models\User::TIPO_ESTUDANTE)
            ->update(['tipo_usuario' => 'ESTUDANTE']);
    }
};
