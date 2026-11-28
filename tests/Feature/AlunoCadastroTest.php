<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\Turma;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AlunoCadastroTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->withoutVite();
    }

    #[DataProvider('cargosPermitidos')]
    public function test_cargos_nao_docentes_podem_acessar_e_cadastrar(string $cargo): void
    {
        $user = $this->usuario($cargo);

        $this->actingAs($user)->get(route('alunos.create'))->assertOk()
            ->assertViewIs('alunos.create')
            ->assertSee('Ficha do Aluno')
            ->assertDontSee('name="password"', false)
            ->assertDontSee('name="email"', false)
            ->assertDontSee('name="turma_id"', false);

        $this->blade('<x-sidebar-menu />')->assertSeeInOrder(['Central de Cadastros', 'Cadastrar Aluno'])
            ->assertSee(route('alunos.create'), false);

        $this->post(route('alunos.store'), $this->dados())->assertRedirect(route('alunos.create'))
            ->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertDatabaseHas('alunos', $this->dados() + ['user_id' => null]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('matriculas', 0);
        $this->assertDatabaseCount('enturmacoes', 0);
        $this->get(route('alunos.create'))->assertOk()->assertSee('Aluno cadastrado com sucesso!');
    }

    public static function cargosPermitidos(): array
    {
        return [
            'administrador' => [User::TIPO_ADMINISTRADOR],
            'gestor' => [User::TIPO_GESTOR],
            'secretaria' => [User::TIPO_SECRETARIA],
            'pedagogo' => [User::TIPO_PEDAGOGO],
            'coordenador' => [User::TIPO_COORDENADOR],
        ];
    }

    #[DataProvider('cargosDocentes')]
    public function test_docentes_nao_veem_menu_nem_acessam_rotas_mesmo_com_permissao(string $cargo): void
    {
        $user = $this->usuario($cargo);
        $user->givePermissionTo('gerenciar estudantes');
        $this->actingAs($user);

        $this->blade('<x-sidebar-menu />')->assertDontSee('Cadastrar Aluno')
            ->assertDontSee(route('alunos.create'), false);
        $this->get(route('alunos.create'))->assertForbidden();
        $this->post(route('alunos.store'), $this->dados())->assertForbidden();
        $this->assertDatabaseCount('alunos', 0);
    }

    #[DataProvider('cargosDocentes')]
    public function test_role_docente_bloqueia_cadastro_mesmo_com_tipo_administrador(string $cargo): void
    {
        $user = $this->usuario(User::TIPO_ADMINISTRADOR);
        $user->assignRole($cargo);
        $this->actingAs($user);

        $this->blade('<x-sidebar-menu />')->assertDontSee('Cadastrar Aluno');
        $this->get(route('alunos.create'))->assertForbidden();
        $this->post(route('alunos.store'), $this->dados())->assertForbidden();
        $this->assertDatabaseCount('alunos', 0);
    }

    public static function cargosDocentes(): array
    {
        return [
            'professor' => [User::TIPO_PROFESSOR],
            'estudo orientado' => [User::TIPO_PROF_ESTUDO_ORIENTADO],
            'educacao especial' => [User::TIPO_PROF_ESPECIAL],
        ];
    }

    public function test_tipo_docente_bloqueia_cadastro_sem_role(): void
    {
        $user = User::factory()->create(['tipo_usuario' => User::TIPO_PROFESSOR]);
        $this->actingAs($user);

        $this->blade('<x-sidebar-menu />')->assertDontSee('Cadastrar Aluno');
        $this->get(route('alunos.create'))->assertForbidden();
        $this->post(route('alunos.store'), $this->dados())->assertForbidden();
        $this->assertDatabaseCount('alunos', 0);
    }

    public function test_visitantes_precisam_autenticar(): void
    {
        $this->get(route('alunos.create'))->assertRedirect(route('login'));
        $this->post(route('alunos.store'), $this->dados())->assertRedirect(route('login'));
        $this->assertDatabaseCount('alunos', 0);
    }

    public function test_cadastro_ignora_campos_de_acesso_e_vinculos_enviados_manualmente(): void
    {
        $user = $this->usuario(User::TIPO_SECRETARIA);
        $contagens = collect(['users', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions'])
            ->mapWithKeys(fn ($tabela) => [$tabela => DB::table($tabela)->count()]);

        $this->actingAs($user)->post(route('alunos.store'), $this->dados() + [
            'user_id' => $user->id,
            'turma_id' => 999,
            'matricula_id' => 999,
            'email' => 'aluno@example.test',
            'password' => 'senha-injetada',
            'tipo_usuario' => User::TIPO_ADMINISTRADOR,
            'roles' => [User::TIPO_ADMINISTRADOR],
        ])->assertRedirect(route('alunos.create'))->assertSessionHasNoErrors();

        $aluno = Aluno::sole();
        $this->assertNull($aluno->user_id);
        $this->assertSame('001234567', $aluno->ra);
        foreach ($contagens as $tabela => $quantidade) {
            $this->assertDatabaseCount($tabela, $quantidade);
        }
        $this->assertDatabaseCount('matriculas', 0);
        $this->assertDatabaseCount('enturmacoes', 0);
        $this->assertDatabaseCount('aluno_eletiva', 0);
    }

    public function test_campos_opcionais_podem_ficar_vazios(): void
    {
        $this->actingAs($this->usuario(User::TIPO_SECRETARIA))->post(route('alunos.store'), [
            'nome' => 'Aluno sem dados opcionais',
            'ra' => '000001',
            'status_matricula' => 'Novato',
            'nascimento' => '',
            'sexo' => '',
            'cep' => '',
        ])->assertRedirect(route('alunos.create'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('alunos', [
            'ra' => '000001', 'user_id' => null, 'nascimento' => null, 'sexo' => null, 'cep' => null,
        ]);
    }

    public function test_ra_duplicado_nao_sobrescreve_aluno_e_preserva_formulario(): void
    {
        $existente = Aluno::create($this->dados(['nome' => 'Aluno já cadastrado']));
        $dados = $this->dados(['status_matricula' => 'Transferência']);
        $this->actingAs($this->usuario(User::TIPO_SECRETARIA));

        $resposta = $this->post(route('alunos.store'), $dados)
            ->assertRedirect(route('alunos.create'))
            ->assertSessionHasErrors(['ra' => 'Já existe um aluno cadastrado com este RA.']);
        foreach ($dados as $campo => $valor) {
            $resposta->assertSessionHasInput($campo, $valor);
        }

        $pagina = $this->withCookie(config('session.cookie'), session()->getId())
            ->get(route('alunos.create'))->assertOk()
            ->assertSee('Já existe um aluno cadastrado com este RA.')
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('value="F" selected', false)
            ->assertSee('value="Transferência" selected', false);
        foreach (array_diff_key($dados, array_flip(['sexo', 'status_matricula'])) as $valor) {
            $pagina->assertSee('value="'.$valor.'"', false);
        }

        $this->assertDatabaseCount('alunos', 1);
        $this->assertSame('Aluno já cadastrado', $existente->fresh()->nome);
    }

    #[DataProvider('dadosInvalidos')]
    public function test_valida_dados_no_servidor(array $alteracoes, array $campos): void
    {
        $this->actingAs($this->usuario(User::TIPO_SECRETARIA))
            ->post(route('alunos.store'), $this->dados($alteracoes))
            ->assertRedirect(route('alunos.create'))->assertSessionHasErrors($campos);

        $this->assertDatabaseCount('alunos', 0);
    }

    public static function dadosInvalidos(): array
    {
        return [
            'obrigatorios vazios' => [['nome' => ' ', 'ra' => '', 'status_matricula' => ''], ['nome', 'ra', 'status_matricula']],
            'tipos invalidos' => [['nome' => ['Nome'], 'ra' => ['123']], ['nome', 'ra']],
            'textos longos' => [array_fill_keys(['nome', 'ra', 'telefone', 'nome_mae', 'telefone_responsavel', 'logradouro'], str_repeat('a', 256)), ['nome', 'ra', 'telefone', 'nome_mae', 'telefone_responsavel', 'logradouro']],
            'data inexistente' => [['nascimento' => '2010-02-30'], ['nascimento']],
            'data fora do formato' => [['nascimento' => '20/05/2010'], ['nascimento']],
            'data futura' => [['nascimento' => '2999-01-01'], ['nascimento']],
            'sexo invalido' => [['sexo' => 'X'], ['sexo']],
            'cep invalido' => [['cep' => 'abc'], ['cep']],
            'status invalido' => [['status_matricula' => 'Inexistente'], ['status_matricula']],
        ];
    }

    public function test_aluno_cadastrado_pode_ser_matriculado_e_vinculado_pelo_fluxo_existente(): void
    {
        $this->actingAs($this->usuario(User::TIPO_SECRETARIA))
            ->post(route('alunos.store'), $this->dados(['cep' => '29000000']))
            ->assertSessionHasNoErrors();
        $aluno = Aluno::sole();
        $turma = Turma::create([
            'modalidade' => 'Ensino Médio', 'turno' => 'Matutino', 'serie' => '1',
            'ano_letivo' => date('Y'), 'ativa' => true,
        ]);

        $this->get(route('alunos.index'))->assertOk()->assertSee($aluno->nome);
        $this->get(route('alunos.show', $aluno))->assertRedirect(route('alunos.edit', $aluno));
        $this->get(route('alunos.edit', $aluno))->assertOk()->assertSee($aluno->nome);
        $this->get(route('vinculo.create'))->assertOk()->assertSee($aluno->nome);
        $this->post(route('vinculo.store'), [
            'aluno_id' => $aluno->id, 'turma_id' => $turma->id, 'tipo_vinculo' => 'REGULAR',
        ])->assertRedirect(route('vinculo.create'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('matriculas', ['aluno_id' => $aluno->id, 'ano_letivo' => date('Y')]);
        $this->assertDatabaseHas('enturmacoes', [
            'matricula_id' => $aluno->matriculas()->sole()->id, 'turma_id' => $turma->id,
            'tipo_vinculo' => 'REGULAR', 'status' => 'Ativo',
        ]);
        $this->assertNull($aluno->fresh()->user_id);
        $this->assertDatabaseCount('users', 1);
    }

    private function usuario(string $cargo): User
    {
        $user = User::factory()->create(['tipo_usuario' => $cargo]);
        $user->assignRole($cargo);

        return $user;
    }

    private function dados(array $alteracoes = []): array
    {
        return array_replace([
            'nome' => 'Maria da Silva',
            'ra' => '001234567',
            'nascimento' => '2010-05-20',
            'sexo' => 'F',
            'telefone' => '(27) 99999-0000',
            'nome_mae' => 'Ana da Silva',
            'telefone_responsavel' => '(27) 98888-0000',
            'cep' => '29000-000',
            'logradouro' => 'Rua das Flores, 100',
            'status_matricula' => 'Novato',
        ], $alteracoes);
    }
}
