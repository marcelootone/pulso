<?php

namespace Tests\Feature;

use App\Models\Eletiva;
use App\Models\User;
use App\Models\Aluno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiarioEletivaNotasTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_save_notas()
    {
        $user = User::factory()->create(['role' => 'Gestor']);
        $eletiva = Eletiva::factory()->create(['usa_nota' => true]);
        $aluno1 = Aluno::factory()->create();
        $aluno2 = Aluno::factory()->create();
        
        $eletiva->alunos()->attach([$aluno1->id, $aluno2->id]);

        $response = $this->actingAs($user)->post(route('eletivas.diario.notas', $eletiva->id), [
            'data_avaliacao' => '2023-10-10',
            'descricao' => 'Prova 1',
            'notas' => [
                $aluno1->id => '9.5',
                $aluno2->id => null, // Testing empty note
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('notas_eletivas', [
            'aluno_id' => $aluno1->id,
            'nota' => '9.50'
        ]);
    }
}
