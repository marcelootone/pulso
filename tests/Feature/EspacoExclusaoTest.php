<?php

namespace Tests\Feature;

use App\Models\Espaco;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EspacoExclusaoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
    }

    public function test_usuario_com_permissao_pode_excluir_espaco(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('gerenciar espacos', 'web'));
        $espaco = Espaco::create(['nome' => 'Laboratório', 'tipo' => 'espaco', 'status' => true]);

        $this->actingAs($user)
            ->delete(route('espacos.destroy', $espaco))
            ->assertRedirect(route('espacos.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($espaco);
    }

    public function test_usuario_sem_permissao_nao_pode_excluir_espaco(): void
    {
        $espaco = Espaco::create(['nome' => 'Projetor', 'tipo' => 'equipamento', 'status' => true]);

        $this->actingAs(User::factory()->create())
            ->delete(route('espacos.destroy', $espaco))
            ->assertForbidden();

        $this->assertModelExists($espaco);
    }
}
