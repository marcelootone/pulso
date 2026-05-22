<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Migrate aluno_turma data
        $alunoTurmas = DB::table('aluno_turma')->get();
        
        $anoLetivo = date('Y');
        
        foreach ($alunoTurmas as $at) {
            // Find or create matricula for the student
            $matricula = DB::table('matriculas')
                ->where('aluno_id', $at->aluno_id)
                ->where('ano_letivo', $anoLetivo)
                ->first();
                
            if (!$matricula) {
                $matriculaId = DB::table('matriculas')->insertGetId([
                    'aluno_id' => $at->aluno_id,
                    'ano_letivo' => $anoLetivo,
                    'status' => 'Ativa',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $matriculaId = $matricula->id;
            }
            
            // Insert into enturmacoes
            DB::table('enturmacoes')->insert([
                'matricula_id' => $matriculaId,
                'turma_id' => $at->turma_id,
                'tipo_vinculo' => 'REGULAR',
                'data_entrada' => now(),
                'status' => 'Ativo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // 2. Drop old tables
        Schema::dropIfExists('aluno_turma');
        Schema::dropIfExists('aluno_eletiva');
        Schema::dropIfExists('eletivas');
    }

    public function down(): void
    {
        // Down not fully supported as data is lost during drop
    }
};
