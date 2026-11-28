<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Espaco;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AgendamentoPeriodoTest extends TestCase
{
    private Espaco $espaco;

    protected function setUp(): void
    {
        parent::setUp();

        // Nunca usa o banco configurado no .env da aplicação.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['tipo_usuario' => User::TIPO_PROFESSOR]));
        $this->espaco = Espaco::create(['nome' => 'Sala de teste', 'tipo' => 'espaco', 'status' => true]);
        $this->travelTo(CarbonImmutable::parse('2026-11-17 12:00:00', 'America/Sao_Paulo'));
    }

    #[DataProvider('datasPermitidas')]
    public function test_permite_reservas_ate_o_limite(string $hoje, string $data, string $tipo): void
    {
        $this->travelTo(CarbonImmutable::parse($hoje, 'America/Sao_Paulo'));
        $this->espaco->update(['tipo' => $tipo]);

        $this->post(route('agendamentos.store', $this->espaco), $this->reserva($data))
            ->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertDatabaseHas('agendamentos', ['espaco_id' => $this->espaco->id, 'data' => $data]);
    }

    public static function datasPermitidas(): array
    {
        return [
            ['2026-11-17', '2026-11-17', 'espaco'],
            ['2026-11-17', '2026-12-17', 'espaco'],
            ['2026-11-17', '2026-12-17', 'equipamento'],
            ['2027-01-31', '2027-02-28', 'espaco'],
            ['2028-01-31', '2028-02-29', 'espaco'],
            ['2026-12-17', '2027-01-17', 'espaco'],
        ];
    }

    #[DataProvider('datasBloqueadas')]
    public function test_bloqueia_datas_invalidas_no_post_e_na_url(string $data): void
    {
        $url = route('agendamentos.create', $this->espaco);

        $this->post(route('agendamentos.store', $this->espaco), $this->reserva($data))
            ->assertRedirect($url)->assertSessionHasErrors('data');
        $this->get($url.'?data='.urlencode($data))
            ->assertRedirect($url)->assertSessionHasErrors('data');
        $this->get($url)->assertOk();
        $this->assertDatabaseCount('agendamentos', 0);
    }

    public static function datasBloqueadas(): array
    {
        return [['2026-11-16'], ['2026-12-18'], ['2026-02-30'], ['invalida'], ['']];
    }

    public function test_calendario_respeita_o_dia_em_brasilia(): void
    {
        // Em UTC já é dia 18; em Brasília ainda é dia 17.
        $this->travelTo(CarbonImmutable::parse('2026-11-18 01:00:00', 'UTC'));
        $this->assertSame('America/Sao_Paulo', config('app.timezone'));
        $this->get(route('agendamentos.create', $this->espaco))
            ->assertOk()
            ->assertSee('min="2026-11-17"', false)
            ->assertSee('max="2026-12-17"', false)
            ->assertSee('17/12/2026');
        $this->post(route('agendamentos.store', $this->espaco), $this->reserva('2026-12-18'))
            ->assertSessionHasErrors('data');
    }

    public function test_mantem_bloqueio_de_conflito_e_reservas_existentes(): void
    {
        Agendamento::create($this->reserva('2026-12-17') + ['espaco_id' => $this->espaco->id, 'user_id' => auth()->id()]);
        Agendamento::create($this->reserva('2027-02-01') + ['espaco_id' => $this->espaco->id, 'user_id' => auth()->id()]);

        $this->post(route('agendamentos.store', $this->espaco), $this->reserva('2026-12-17'))
            ->assertSessionHas('error');
        $this->assertDatabaseCount('agendamentos', 2);
        $this->assertDatabaseHas('agendamentos', ['data' => '2027-02-01']);
    }

    private function reserva(string $data): array
    {
        return ['data' => $data, 'horario_inicio' => '09:00', 'horario_fim' => '10:00'];
    }
}
