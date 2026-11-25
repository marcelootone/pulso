<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Espaco;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendamentoEdicaoTest extends TestCase
{
    private Agendamento $reserva;
    private User $autor;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->withoutVite();
        $this->travelTo(CarbonImmutable::parse('2026-11-17 12:00:00', 'America/Sao_Paulo'));
        $this->autor = User::factory()->create(['tipo_usuario' => User::TIPO_PROFESSOR]);
        $espaco = Espaco::create(['nome' => 'Projetor', 'tipo' => 'equipamento', 'status' => true]);
        $this->reserva = Agendamento::create([
            'espaco_id' => $espaco->id, 'user_id' => $this->autor->id,
            'data' => '2026-11-18', 'horario_inicio' => '09:00', 'horario_fim' => '10:00', 'motivo' => 'Aula',
        ]);
    }

    #[DataProvider('perfis')]
    public function test_autorizacao_na_interface_e_no_backend(string $perfil, bool $autor, bool $permitido): void
    {
        $user = $autor ? $this->autor : User::factory()->create(['tipo_usuario' => $perfil]);
        $this->actingAs($user);
        $link = route('agendamentos.edit', $this->reserva);
        $pagina = $this->get(route('agendamentos.create', ['espaco' => $this->reserva->espaco_id, 'data' => $this->reserva->data]));
        $pagina->assertOk();
        if ($permitido) {
            $pagina->assertSee($link, false);
            $this->get($link)->assertOk()->assertSee('Aula');
            $this->put(route('agendamentos.update', $this->reserva), $this->dados() + ['user_id' => $user->id, 'espaco_id' => 999])
                ->assertSessionHasNoErrors()->assertSessionHas('success');
            $this->assertSame('Alterada', $this->reserva->fresh()->motivo);
            $this->assertEquals($this->autor->id, $this->reserva->fresh()->user_id);
            $this->assertEquals($this->reserva->espaco_id, $this->reserva->fresh()->espaco_id);
        } else {
            $pagina->assertDontSee($link, false);
            $this->get($link)->assertForbidden();
            $this->put(route('agendamentos.update', $this->reserva), $this->dados())->assertForbidden();
            $this->assertSame('Aula', $this->reserva->fresh()->motivo);
        }
        $this->assertDatabaseCount('agendamentos', 1);
    }

    public static function perfis(): array
    {
        return [
            [User::TIPO_PROFESSOR, true, true],
            [User::TIPO_PROFESSOR, false, false],
            [User::TIPO_COORDENADOR, false, false],
            [User::TIPO_SECRETARIA, false, true],
            [User::TIPO_PEDAGOGO, false, true],
            [User::TIPO_GESTOR, false, true],
            [User::TIPO_ADMINISTRADOR, false, true],
        ];
    }

    public function test_reconhece_perfil_atribuido_pelo_spatie(): void
    {
        $user = User::factory()->create(['tipo_usuario' => User::TIPO_PROFESSOR]);
        $user->assignRole(Role::create(['name' => User::TIPO_SECRETARIA, 'guard_name' => 'web']));
        $this->actingAs($user)->get(route('agendamentos.edit', $this->reserva))->assertOk();
    }

    #[DataProvider('gestao')]
    public function test_nem_gestao_edita_reserva_iniciada(string $perfil): void
    {
        $this->reserva->update(['data' => '2026-11-17', 'horario_inicio' => '12:00', 'horario_fim' => '13:00']);
        $this->actingAs(User::factory()->create(['tipo_usuario' => $perfil]));
        $this->get(route('agendamentos.edit', $this->reserva))->assertForbidden();
        $this->put(route('agendamentos.update', $this->reserva), $this->dados())->assertForbidden();
        $this->get(route('agendamentos.create', ['espaco' => $this->reserva->espaco_id]))
            ->assertDontSee(route('agendamentos.edit', $this->reserva), false);
    }

    public static function gestao(): array
    {
        return [[User::TIPO_SECRETARIA], [User::TIPO_PEDAGOGO], [User::TIPO_GESTOR], [User::TIPO_ADMINISTRADOR]];
    }

    public function test_autor_nao_edita_reserva_encerrada(): void
    {
        $this->reserva->update(['data' => '2026-11-16']);
        $this->actingAs($this->autor)->put(route('agendamentos.update', $this->reserva), $this->dados())->assertForbidden();
    }

    public function test_conflito_preserva_reserva_e_horario_adjacente_e_permitido(): void
    {
        Agendamento::create($this->dados() + ['espaco_id' => $this->reserva->espaco_id, 'user_id' => $this->autor->id]);
        $this->actingAs($this->autor);
        $this->put(route('agendamentos.update', $this->reserva), $this->dados())
            ->assertRedirect(route('agendamentos.edit', $this->reserva))->assertSessionHasErrors('horario_inicio');
        $this->assertSame('Aula', $this->reserva->fresh()->motivo);
        $this->put(route('agendamentos.update', $this->reserva), array_replace($this->dados(), ['horario_inicio' => '10:00', 'horario_fim' => '11:00']))
            ->assertSessionHasNoErrors()->assertSessionHas('success');
    }

    #[DataProvider('dadosInvalidos')]
    public function test_rejeita_alteracoes_invalidas(array $alteracoes, string $campo): void
    {
        $this->actingAs($this->autor)->put(route('agendamentos.update', $this->reserva), array_replace($this->dados(), $alteracoes))
            ->assertRedirect(route('agendamentos.edit', $this->reserva))->assertSessionHasErrors($campo);
        $this->assertSame('Aula', $this->reserva->fresh()->motivo);
    }

    public static function dadosInvalidos(): array
    {
        return [
            [['data' => '2026-12-18'], 'data'],
            [['data' => '2026-11-16'], 'data'],
            [['data' => '2026-11-17'], 'horario_inicio'],
            [['horario_fim' => '08:00'], 'horario_fim'],
            [['horario_inicio' => 'invalido'], 'horario_inicio'],
        ];
    }

    private function dados(): array
    {
        return ['data' => '2026-11-18', 'horario_inicio' => '09:00', 'horario_fim' => '10:00', 'motivo' => 'Alterada'];
    }
}
