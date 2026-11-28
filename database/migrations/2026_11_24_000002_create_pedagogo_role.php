<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $pedagogo = Role::firstOrCreate(['name' => User::TIPO_PEDAGOGO, 'guard_name' => 'web']);
        $secretaria = Role::where('name', User::TIPO_SECRETARIA)
            ->where('guard_name', 'web')
            ->first();

        if ($secretaria) {
            $pedagogo->syncPermissions($secretaria->permissions);
        }
    }

    public function down(): void
    {
        Role::where('name', User::TIPO_PEDAGOGO)
            ->where('guard_name', 'web')
            ->get()
            ->each
            ->delete();
    }
};
