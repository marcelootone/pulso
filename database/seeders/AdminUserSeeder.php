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
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Administrador do Sistema',
                'password' => \Illuminate\Support\Facades\Hash::make('senha123'),
                'tipo_usuario' => 'Administrador',
                'ra' => 'ADMIN01', // O campo RA está como fillable na tabela users e pode ser necessário em alguns lugares do sistema.
            ]
        );
    }
}
