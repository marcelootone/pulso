<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$eletiva = \App\Models\Eletiva::find(3);
$user = \App\Models\User::first();
auth()->login($user);

request()->merge(['tab' => 'notas']);

$html = view('eletivas.diario', [
    'eletiva' => $eletiva, 
    'dataSelecionada' => '2023-10-10', 
    'dataAvaliacao' => '2023-10-10', 
    'descricaoAvaliacao' => 'Teste', 
    'frequencias' => [], 
    'notas' => [], 
    'avaliacoes' => collect([]),
])->withErrors(new \Illuminate\Support\MessageBag())->render();

if (strpos($html, 'name="data_avaliacao"') !== false) {
    echo "data_avaliacao input FOUND\n";
    $start = strpos($html, 'name="data_avaliacao"');
    echo substr($html, $start - 20, 200);
    echo "\n";
} else {
    echo "data_avaliacao input NOT FOUND\n";
}
