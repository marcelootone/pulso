<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Espaco;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AgendamentoAgendaTest extends TestCase
{
    private Espaco $espaco;
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
        $this->actingAs($this->autor);
        $this->espaco = Espaco::create(['nome' => 'Laboratório', 'tipo' => 'espaco', 'status' => true]);
    }

    public function test_agenda_separa_historico_inclui_em_andamento_e_isola_espaco(): void
    {
        $this->reserva(['data' => '2026-11-16', 'motivo' => 'Reserva passada']);
        $this->reserva(['data' => '2026-11-17', 'horario_inicio' => '11:00:00', 'horario_fim' => '12:00:00', 'motivo' => 'Terminou agora']);
        $this->reserva(['data' => '2026-11-17', 'horario_inicio' => '11:30:00', 'horario_fim' => '12:30:00', 'motivo' => 'Aula em curso']);
        $futura = $this->reserva(['motivo' => 'Reserva futura']);
        $this->reserva(['data' => '2027-02-01', 'motivo' => 'Reserva distante']);
        $outro = Espaco::create(['nome' => 'Outro espaço', 'tipo' => 'espaco', 'status' => true]);
        $this->reserva(['espaco_id' => $outro->id, 'motivo' => 'Outro recurso']);

        $this->get(route('agendamentos.agenda', $this->espaco))->assertOk()
            ->assertSeeInOrder(['Aula em curso', 'Reserva futura', 'Reserva distante'])
            ->assertSee('Em andamento')->assertDontSee('Reserva passada')->assertDontSee('Terminou agora')
            ->assertDontSee('Outro recurso')->assertSee(route('agendamentos.edit', $futura), false);
        $this->get(route('agendamentos.agenda', ['espaco' => $this->espaco, 'historico' => 1]))->assertOk()
            ->assertSeeInOrder(['Terminou agora', 'Reserva passada'])->assertDontSee('Aula em curso')
            ->assertDontSee('Reserva futura')->assertDontSee(route('agendamentos.edit', $futura), false);
    }

    public function test_links_estados_vazios_e_retorno_apos_salvar(): void
    {
        $agenda = route('agendamentos.agenda', $this->espaco);
        $this->get(route('agendamentos.index'))->assertOk()->assertSee($agenda, false);
        $this->get($agenda)->assertOk()->assertSee('Nenhum agendamento futuro')
            ->assertSee(route('agendamentos.create', $this->espaco), false);
        $dados = ['data' => '2026-11-18', 'horario_inicio' => '09:00', 'horario_fim' => '10:00'];
        $this->post(route('agendamentos.store', $this->espaco), $dados)->assertRedirect($agenda);
        $reserva = Agendamento::firstOrFail();
        $this->put(route('agendamentos.update', $reserva), $dados + ['motivo' => 'Atualizada'])->assertRedirect($agenda);
        $this->get($agenda)->assertSee('Atualizada');
    }

    public function test_agenda_nao_mostra_edicao_para_terceiros(): void
    {
        $reserva = $this->reserva();
        $this->actingAs(User::factory()->create(['tipo_usuario' => User::TIPO_PROFESSOR]));
        $this->get(route('agendamentos.agenda', $this->espaco))->assertOk()
            ->assertDontSee(route('agendamentos.edit', $reserva), false);
    }

    public function test_paginacao_preserva_historico_e_agrupa_por_data(): void
    {
        for ($i = 0; $i < 31; $i++) {
            $this->reserva(['data' => '2026-11-16']);
        }
        $url = route('agendamentos.agenda', ['espaco' => $this->espaco, 'historico' => 1]);
        $this->get($url)->assertOk()->assertSee('historico=1', false)
            ->assertViewHas('reservasPorDia', fn ($dias) => $dias->count() === 1 && $dias->first()->count() === 30);
        $this->get($url.'&page=2')->assertOk()
            ->assertViewHas('agendamentos', fn ($reservas) => $reservas->count() === 1);
    }

    private function reserva(array $dados = []): Agendamento
    {
        return Agendamento::create(array_replace([
            'espaco_id' => $this->espaco->id, 'user_id' => $this->autor->id,
            'data' => '2026-11-18', 'horario_inicio' => '09:00:00', 'horario_fim' => '10:00:00', 'motivo' => 'Aula',
        ], $dados));
    }
}
