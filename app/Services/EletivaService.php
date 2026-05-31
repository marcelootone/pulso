<?php

namespace App\Services;

use App\Models\Eletiva;
use Illuminate\Support\Facades\DB;

class EletivaService
{
    public function criarEletiva(array $data): Eletiva
    {
        return DB::transaction(function () use ($data) {
            $eletiva = Eletiva::create([
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
                'tipo' => $data['tipo'],
                'vagas' => $data['vagas'],
                'usa_nota' => $data['usa_nota'] ?? false,
                'ano_letivo' => $data['ano_letivo'],
                'ativa' => true,
            ]);

            if (!empty($data['professor_ids'])) {
                $eletiva->professores()->attach($data['professor_ids']);
            }

            return $eletiva;
        });
    }

    public function atualizarEletiva(Eletiva $eletiva, array $data): Eletiva
    {
        return DB::transaction(function () use ($eletiva, $data) {
            $eletiva->update([
                'nome' => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
                'tipo' => $data['tipo'],
                'vagas' => $data['vagas'],
                'usa_nota' => $data['usa_nota'] ?? false,
                'ano_letivo' => $data['ano_letivo'],
            ]);

            if (isset($data['professor_ids'])) {
                $eletiva->professores()->sync($data['professor_ids']);
            }

            return $eletiva;
        });
    }

    public function toggleAtiva(Eletiva $eletiva): void
    {
        $eletiva->ativa = !$eletiva->ativa;
        $eletiva->save();
    }

    public function inscreverAlunos(Eletiva $eletiva, array $alunoIds): void
    {
        DB::transaction(function () use ($eletiva, $alunoIds) {
            $syncData = [];
            foreach ($alunoIds as $alunoId) {
                $syncData[$alunoId] = [
                    'data_inscricao' => now()->toDateString(),
                    'status' => 'Ativo'
                ];
            }
            
            // Usamos syncWithoutDetaching para não remover os alunos já existentes
            $eletiva->alunos()->syncWithoutDetaching($syncData);
        });
    }

    public function removerAluno(Eletiva $eletiva, int $alunoId): void
    {
        $eletiva->alunos()->updateExistingPivot($alunoId, [
            'status' => 'Removido',
            'data_saida' => now()->toDateString()
        ]);
    }

    public function transferirAluno(int $alunoId, Eletiva $origem, Eletiva $destino): void
    {
        DB::transaction(function () use ($alunoId, $origem, $destino) {
            // Marca como transferido na origem
            $origem->alunos()->updateExistingPivot($alunoId, [
                'status' => 'Transferido',
                'data_saida' => now()->toDateString()
            ]);

            // Adiciona no destino
            $destino->alunos()->syncWithoutDetaching([
                $alunoId => [
                    'data_inscricao' => now()->toDateString(),
                    'status' => 'Ativo'
                ]
            ]);
        });
    }
}
