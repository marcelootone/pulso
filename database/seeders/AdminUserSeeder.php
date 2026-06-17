<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Administrador do Sistema',
                'password' => \Illuminate\Support\Facades\Hash::make('senha123'),
                'tipo_usuario' => 'Administrador',
                'ra' => 'ADMIN01',
            ]
        );
        $admin->assignRole('Administrador');

        $gestor = \App\Models\User::updateOrCreate(
            ['email' => 'gestor1@email.com'],
            [
                'name' => 'Gestor Teste',
                'password' => \Illuminate\Support\Facades\Hash::make('senha123'),
                'tipo_usuario' => 'Gestor',
                'ra' => 'GESTOR01',
            ]
        );
        $gestor->assignRole('Gestor');
    }
}
