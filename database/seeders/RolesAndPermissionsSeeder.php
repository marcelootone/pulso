<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar cache do spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Criar as Permissões
        $permissoes = [
            // Administrativo (Secretaria)
            'gerenciar estudantes',
            'gerenciar turmas',
            'gerenciar professores',
            'gerenciar eletivas',
            'gerenciar espacos',
            'gerenciar horarios',
            'realizar matriculas',
            'vincular estudantes eletivas',
            'ver relatorios administrativos',
            'ver frequencia geral',
            'agendar espacos',
            'emitir documentos',
            
            // Pedagógico Global (Coordenador)
            'ver todos diarios',
            'acompanhar frequencia',
            'acompanhar rendimento',
            'validar lancamentos',
            'ver relatorios pedagogicos',
            'acompanhar evasao',
            
            // Docência (Professor)
            'acessar turmas vinculadas',
            'lancar frequencia',
            'lancar conteudos',
            'lancar avaliacoes',
            'lancar notas',
            'acessar proprias eletivas',
            'ver estudantes',
            'ver relatorios proprias turmas'
        ];

        foreach ($permissoes as $permissao) {
            Permission::firstOrCreate(['name' => $permissao, 'guard_name' => 'web']);
        }

        // 2. Criar as Roles (Perfis) e vincular as permissões

        // GESTOR (Acesso irrestrito implementado no Gate::before do AuthServiceProvider)
        $roleGestor = Role::firstOrCreate(['name' => User::TIPO_GESTOR, 'guard_name' => 'web']);
        
        // ADMINISTRADOR (Acesso irrestrito implementado no Gate::before)
        $roleAdmin = Role::firstOrCreate(['name' => User::TIPO_ADMINISTRADOR, 'guard_name' => 'web']);
        
        // SECRETARIA
        $roleSecretaria = Role::firstOrCreate(['name' => User::TIPO_SECRETARIA, 'guard_name' => 'web']);
        $roleSecretaria->syncPermissions([
            'gerenciar estudantes',
            'gerenciar turmas',
            'gerenciar professores',
            'gerenciar eletivas',
            'gerenciar espacos',
            'gerenciar horarios',
            'realizar matriculas',
            'vincular estudantes eletivas',
            'ver relatorios administrativos',
            'ver frequencia geral',
            'agendar espacos',
            'emitir documentos'
        ]);

        // COORDENADOR
        $roleCoordenador = Role::firstOrCreate(['name' => User::TIPO_COORDENADOR, 'guard_name' => 'web']);
        $roleCoordenador->syncPermissions([
            'ver todos diarios',
            'acompanhar frequencia',
            'acompanhar rendimento',
            'validar lancamentos',
            'gerenciar eletivas', // Tem gestão total de eletivas também
            'vincular estudantes eletivas',
            'ver relatorios pedagogicos',
            'acompanhar evasao',
            'agendar espacos'
        ]);

        // PROFESSOR
        $roleProfessor = Role::firstOrCreate(['name' => User::TIPO_PROFESSOR, 'guard_name' => 'web']);
        $roleProfessor->syncPermissions([
            'acessar turmas vinculadas',
            'lancar frequencia',
            'lancar conteudos',
            'lancar avaliacoes',
            'lancar notas',
            'acessar proprias eletivas',
            'ver estudantes',
            'agendar espacos',
            'ver relatorios proprias turmas'
        ]);

        // ESTUDANTE (Base)
        $roleEstudante = Role::firstOrCreate(['name' => User::TIPO_ESTUDANTE, 'guard_name' => 'web']);
        // O estudante, por enquanto, tem acessos de leitura básicos baseados no seu RA e Auth default

        // Tipos específicos
        $roleProfEspecial = Role::firstOrCreate(['name' => User::TIPO_PROF_ESPECIAL, 'guard_name' => 'web']);
        $roleProfOrientado = Role::firstOrCreate(['name' => User::TIPO_PROF_ESTUDO_ORIENTADO, 'guard_name' => 'web']);
        // Mesmas permissões base de professor, expansível depois
        $roleProfEspecial->syncPermissions($roleProfessor->permissions);
        $roleProfOrientado->syncPermissions($roleProfessor->permissions);

        // 3. Sincronizar usuários existentes com as Roles
        $users = User::all();
        foreach ($users as $user) {
            if ($user->tipo_usuario && Role::where('name', $user->tipo_usuario)->exists()) {
                // Atribui a role exata do tipo_usuario
                $user->assignRole($user->tipo_usuario);
            }
        }
    }
}
