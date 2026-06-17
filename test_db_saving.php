<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$eletiva = \App\Models\Eletiva::find(3);
if (!$eletiva) {
    echo "Eletiva 3 not found\n";
    exit;
}

$alunos = $eletiva->alunosAtivos;
$aluno_id = $alunos->first()->id ?? 1;

$controller = app(\App\Http\Controllers\DiarioEletivaController::class);

$request = \Illuminate\Http\Request::create('/eletivas/3/notas', 'POST', [
    'data_avaliacao' => '2023-10-10',
    'descricao' => 'Prova 1',
    'notas' => [
        $aluno_id => '9.5'
    ]
]);

$user = \App\Models\User::where('role', 'Gestor')->first();
auth()->login($user);

try {
    $response = $controller->salvarNota($request, 3);
    dump($response->getSession()->get('errors'));
    dump(\App\Models\NotaEletiva::where('eletiva_id', 3)->get()->toArray());
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
