<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\ImportacaoController;
use App\Http\Controllers\AtribuicaoController;
use App\Http\Controllers\DiarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatorioController;

use App\Http\Controllers\NotaController;

Route::redirect('/', '/login');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Grupo de rotas protegidas APENAS para funcionários
Route::middleware(['auth', 'restrito'])->group(function () {

    // Gerenciamento de Usuários (Acesso restrito)
    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Módulo de Frequência e Busca Ativa
    Route::get('/frequencia', [\App\Http\Controllers\FrequenciaController::class, 'index'])->name('frequencia.index');
    Route::get('/frequencia/monitorar', [\App\Http\Controllers\FrequenciaController::class, 'monitorar'])->name('frequencia.monitorar');
    Route::post('/frequencia/monitorar', [\App\Http\Controllers\FrequenciaController::class, 'store'])->name('frequencia.store');
    Route::get('/frequencia/busca-ativa', [\App\Http\Controllers\FrequenciaController::class, 'buscaAtiva'])->name('frequencia.busca_ativa');
    Route::post('/frequencia/busca-ativa/registrar', [\App\Http\Controllers\FrequenciaController::class, 'registrarBuscaAtiva'])->name('frequencia.registrar_busca_ativa');


    
    // O comando 'resource' cria magicamente as rotas /turmas, /turmas/create, etc.
    Route::resource('turmas', TurmaController::class);
    Route::resource('alunos', \App\Http\Controllers\AlunoController::class)->only(['edit', 'update', 'destroy']);
    Route::get('/importar-alunos', [ImportacaoController::class, 'index'])->name('importar.index');
    Route::post('/importar-alunos/preview', [ImportacaoController::class, 'preview'])->name('importar.preview');
    Route::post('/importar-alunos/confirmar', [ImportacaoController::class, 'confirm'])->name('importar.confirm');
    
    // Vinculação Manual de Aluno a Turma
    Route::get('/vinculo-aluno-turma', [\App\Http\Controllers\VinculoAlunoTurmaController::class, 'create'])->name('vinculo.create');
    Route::post('/vinculo-aluno-turma', [\App\Http\Controllers\VinculoAlunoTurmaController::class, 'store'])->name('vinculo.store');
    Route::get('/atribuir-aulas', [AtribuicaoController::class, 'create'])->name('atribuicoes.create');
    Route::post('/atribuir-aulas', [AtribuicaoController::class, 'store'])->name('atribuicoes.store');
    
    // Relatórios (Acesso restrito)
    Route::get('/relatorios', [\App\Http\Controllers\RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorio-evasao', [\App\Http\Controllers\RelatorioController::class, 'evasao'])->name('relatorios.evasao');
    Route::get('/relatorios/frequencia-mensal', [\App\Http\Controllers\RelatorioController::class, 'frequenciaMensal'])->name('relatorios.frequencia_mensal');
    Route::get('/relatorios/turmas-faltas', [\App\Http\Controllers\RelatorioController::class, 'turmasComMaisFaltas'])->name('relatorios.turmas_faltas');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/meu-diario', [\App\Http\Controllers\DiarioController::class, 'index'])->name('diario.index');
    Route::get('/meu-diario/{id}', [\App\Http\Controllers\DiarioController::class, 'show'])->name('diario.show');
    Route::post('/meu-diario/salvar', [\App\Http\Controllers\DiarioController::class, 'store'])->name('diario.store');
    
    // Rotas de Avaliações
    Route::get('/turmas/{turma}/disciplinas/{disciplina}/avaliacoes', [\App\Http\Controllers\AvaliacaoController::class, 'index'])->name('avaliacoes.index');
    Route::post('/turmas/{turma}/disciplinas/{disciplina}/avaliacoes', [\App\Http\Controllers\AvaliacaoController::class, 'store'])->name('avaliacoes.store');

    // Rotas de Lançamento de Notas em Lote (por avaliação)
    Route::get('/avaliacoes/{avaliacao}/notas', [\App\Http\Controllers\NotaController::class, 'create'])->name('notas.create');
    Route::post('/avaliacoes/{avaliacao}/notas', [\App\Http\Controllers\NotaController::class, 'store'])->name('notas.store');

    // Agendamento de Espaços
    Route::resource('espacos', \App\Http\Controllers\EspacoController::class)->except(['show', 'destroy']);
    Route::get('/agendamentos', [\App\Http\Controllers\AgendamentoController::class, 'index'])->name('agendamentos.index');
    Route::get('/agendamentos/{espaco}/reservar', [\App\Http\Controllers\AgendamentoController::class, 'create'])->name('agendamentos.create');
    Route::post('/agendamentos/{espaco}/reservar', [\App\Http\Controllers\AgendamentoController::class, 'store'])->name('agendamentos.store');

    // Planejamento Semanal
    Route::get('/planejamento-semanal', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'index'])->name('planejamento.index');
    Route::post('/planejamento-semanal/salvar', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'salvar'])->name('planejamento.salvar');
    Route::post('/planejamento-semanal/reordenar', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'reordenar'])->name('planejamento.reordenar');
    Route::post('/planejamento-semanal/horario', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'adicionarHorario'])->name('planejamento.adicionar-horario');
    Route::delete('/planejamento-semanal/horario/{id}', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'removerHorario'])->name('planejamento.remover-horario');
});

require __DIR__.'/auth.php';