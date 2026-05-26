<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
$plan = App\Models\PlanejamentoSemanal::first();
$service = new App\Services\PlanejamentoSemanalService();
$horarioId = $plan->horarios->first()->id;

$dados = [
    'horarios' => [
        $horarioId => [
            'itens' => [
                'TERCA' => [
                    'tarefa' => 'Tutoria Individual',
                    'andamento' => 'CONCLUIDO'
                ]
            ]
        ]
    ]
];

$service->salvarAlteracoes($dados, $plan);

echo "Count: " . App\Models\PlanejamentoItem::where('dia_semana', 'TERCA')->whereNotNull('tarefa')->count() . "\n";
