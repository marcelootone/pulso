<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Matricula;
use App\Models\Enturmacao;
use App\Models\Frequencia;

/**
 * Seeder DETERMINÍSTICO para os testes End-to-End (Playwright).
 *
 * Garante a existência de:
 *  - um usuário administrador com credencial conhecida (admin@example.com / password),
 *    usada pelos specs E2E para autenticar;
 *  - uma turma ativa;
 *  - um estudante com faltas suficientes para disparar os alertas de infrequência
 *    (janelas deslizantes da SEDU-ES 2026), de modo que ele apareça obrigatoriamente
 *    no painel de Busca Ativa.
 *
 * É idempotente: pode ser executado várias vezes sem duplicar dados e sem apagar
 * dados existentes do ambiente (usa updateOrCreate / firstOrCreate).
 */
class E2ETestSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que os papéis/permissões existam antes de atribuir a role.
        $this->call(RolesAndPermissionsSeeder::class);

        // 1. Administrador de teste com credencial conhecida pelos specs E2E.
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin E2E',
                'password' => Hash::make('password'),
                'tipo_usuario' => User::TIPO_ADMINISTRADOR,
                'ra' => 'ADMINE2E',
            ]
        );
        if (! $admin->hasRole(User::TIPO_ADMINISTRADOR)) {
            $admin->assignRole(User::TIPO_ADMINISTRADOR);
        }

        // 2. Turma ativa de teste.
        $turma = Turma::firstOrCreate(
            ['modalidade' => 'EF', 'serie' => '9', 'turno' => 'Matutino', 'complemento' => 'E2E'],
            ['ano_letivo' => (int) date('Y'), 'tipo' => 'REGULAR', 'ativa' => true]
        );

        // 3. Estudante de teste (RA fixo para navegação determinística no spec).
        $aluno = Aluno::updateOrCreate(
            ['ra' => 'E2E0001'],
            [
                'nome' => 'Aluno Teste E2E',
                'nascimento' => '01/01/2010',
                'sexo' => 'M',
                'status_matricula' => 'Ativo',
            ]
        );

        // 4. Matrícula (ano letivo corrente) + enturmação ativa na turma.
        $matricula = Matricula::updateOrCreate(
            ['aluno_id' => $aluno->id, 'ano_letivo' => (int) date('Y')],
            ['etapa' => '9º Ano', 'status' => 'Ativa']
        );

        Enturmacao::updateOrCreate(
            ['matricula_id' => $matricula->id, 'turma_id' => $turma->id],
            [
                'tipo_vinculo' => 'REGULAR',
                'data_entrada' => Carbon::now()->startOfYear()->toDateString(),
                'status' => 'Ativo',
            ]
        );

        // 5. Faltas (status F) em datas distintas e recentes.
        //    8 faltas nos últimos 12 dias garantem o disparo dos alertas:
        //    Semanal (>= 2 dias em 7), Mensal (>= 5 dias em 30) e Trimestral.
        for ($i = 1; $i <= 12; $i++) {
            $data = Carbon::now()->subDays($i);
            // pula fins de semana para simular dias letivos
            if ($data->isWeekend()) {
                continue;
            }
            Frequencia::updateOrCreate(
                [
                    'aluno_id' => $aluno->id,
                    'turma_id' => $turma->id,
                    'data' => $data->toDateString(),
                    'user_id' => $admin->id,
                ],
                ['status' => 'F']
            );
        }

        // 6. Usuários por PAPEL com credenciais determinísticas usadas pelos specs E2E.
        //    Padrão único: <papel>@example.com / "password".
        //    O Administrador (acima) já tem acesso irrestrito via Gate::before; estes
        //    cobrem os testes que validam o comportamento ESPECÍFICO de cada perfil.
        $rolesE2E = [
            'gestor@example.com'      => ['name' => 'Gestor E2E',       'role' => User::TIPO_GESTOR,                 'ra' => 'GESTORE2E'],
            'coordenador@example.com' => ['name' => 'Coordenador E2E',  'role' => User::TIPO_COORDENADOR,            'ra' => 'COORDE2E'],
            'professor@example.com'   => ['name' => 'Professor E2E',    'role' => User::TIPO_PROFESSOR,              'ra' => 'PROFE2E'],
            'professoreo@example.com' => ['name' => 'Professor EO E2E', 'role' => User::TIPO_PROF_ESTUDO_ORIENTADO, 'ra' => 'PROFEOE2E'],
        ];

        $usuarios = [];
        foreach ($rolesE2E as $email => $dados) {
            $u = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $dados['name'],
                    'password' => Hash::make('password'),
                    'tipo_usuario' => $dados['role'],
                    'ra' => $dados['ra'],
                ]
            );
            if (! $u->hasRole($dados['role'])) {
                $u->assignRole($dados['role']);
            }
            $usuarios[$email] = $u;
        }

        // 7. Vincula o professor de teste à turma E2E (pivot professor_turma),
        //    para que os testes de "visibilidade por professor" tenham dados reais.
        $usuarios['professor@example.com']->turmas()->syncWithoutDetaching([
            $turma->id => ['disciplina' => 'Matemática'],
        ]);

        $this->command?->info('E2ETestSeeder: admin@example.com, gestor@example.com, coordenador@example.com, professor@example.com, professoreo@example.com (senha "password"), turma E2E e Aluno Teste E2E (infrequente) prontos.');
    }
}
