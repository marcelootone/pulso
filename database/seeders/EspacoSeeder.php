<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Espaco;

class EspacoSeeder extends Seeder
{
    public function run(): void
    {
        $espacos = [
            'Laboratório de Ciências',
            'Laboratório de Informática',
            'Auditório',
            'Biblioteca',
            'Quadra Poliesportiva',
            'Pátio Externo',
            'Refeitório',
            'Ilha de Chromebooks'
        ];

        foreach ($espacos as $espaco) {
            Espaco::updateOrCreate(['nome' => $espaco], ['status' => true]);
        }
    }
}
