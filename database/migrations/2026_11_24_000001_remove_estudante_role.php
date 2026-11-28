<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('tipo_usuario')->default(null)->change();
        });

        Role::where('name', 'Estudante')
            ->where('guard_name', 'web')
            ->get()
            ->each
            ->delete();
    }

    public function down(): void
    {
        Role::firstOrCreate(['name' => 'Estudante', 'guard_name' => 'web']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('tipo_usuario')->default('Estudante')->change();
        });
    }
};
