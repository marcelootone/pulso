<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dashboardService = app(App\Services\DashboardService::class);
$relatorioService = app(App\Services\RelatorioService::class);

echo "1. Alerta de Evasao\n";
$risco = $dashboardService->getAlunosEmRiscoTodos();
foreach ($risco as $r) {
    echo "Aluno: {$r->nome} - Turma: {$r->serie} {$r->complemento} - Freq: {$r->percentual}%\n";
}

echo "\n2. Frequencia Mensal (Turma 1, mes passado)\n";
$mes = date('m') - 1;
if ($mes == 0) $mes = 12;
$ano = date('Y');
try {
    $turma1 = App\Models\Turma::first()->id ?? 1;
    $freq = $relatorioService->frequenciaMensal($turma1, $mes, $ano);
    echo "Turma: {$freq['turma']->serie} {$freq['turma']->complemento}\n";
    foreach ($freq['alunos'] as $aluno) {
        echo "Aluno: {$aluno['aluno']->nome} - P: {$aluno['total_presencas']} - F: {$aluno['total_faltas']} - %: {$aluno['percentual']}%\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n3. Ranking de Turmas\n";
try {
    $ranking = $relatorioService->rankingTurmasFaltas($mes, $ano);
    foreach ($ranking as $r) {
        echo "Turma: {$r->turma->serie} {$r->turma->complemento} - Ausencia: {$r->percentual_ausencia}%\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
