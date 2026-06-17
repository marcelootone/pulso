<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$v = Illuminate\Support\Facades\Validator::make(['notas' => [1 => '9.5', 2 => null], 'data_avaliacao' => '2023-10-10', 'descricao' => 'Test'], ['data_avaliacao' => 'required|date', 'descricao' => 'required|string|max:255', 'notas' => 'required|array', 'notas.*' => ['nullable', 'numeric', 'min:0', 'max:100', 'regex:/^\d+(\.\d{1,2})?$/']]);
dump($v->fails());
dump($v->errors()->toArray());
