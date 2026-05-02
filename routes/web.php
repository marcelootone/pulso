<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\ImportacaoController;
use App\Http\Controllers\AtribuicaoController;
use App\Http\Controllers\DiarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatorioController; // <-- NOVA IMPORTAÇÃO AQUI
use App\Http\Controllers\EletivaController;
use App\Http\Controllers\NotaController;

Route::get('/', function () {
    return view('welcome');
});

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

    Route::get('/eletivas', [EletivaController::class, 'index'])->name('eletivas.index');
    Route::post('/eletivas', [EletivaController::class, 'store'])->name('eletivas.store');
    Route::get('/eletivas/{id}', [EletivaController::class, 'show'])->name('eletivas.show');
    Route::post('/eletivas/{id}/matricular', [EletivaController::class, 'matricular'])->name('eletivas.matricular');
    Route::delete('/eletivas/{id}/remover/{aluno}', [EletivaController::class, 'remover'])->name('eletivas.remover');
    Route::get('/eletivas/{id}/editar', [EletivaController::class, 'edit'])->name('eletivas.edit');
    Route::put('/eletivas/{id}', [EletivaController::class, 'update'])->name('eletivas.update');
    Route::delete('/eletivas/{id}', [EletivaController::class, 'destroy'])->name('eletivas.destroy');
    
    // O comando 'resource' cria magicamente as rotas /turmas, /turmas/create, etc.
    Route::resource('turmas', TurmaController::class);
    Route::resource('alunos', \App\Http\Controllers\AlunoController::class)->only(['edit', 'update']);
    Route::get('/importar-alunos', [ImportacaoController::class, 'create'])->name('importar.create');
    Route::post('/importar-alunos', [ImportacaoController::class, 'store'])->name('importar.store');
    Route::get('/atribuir-aulas', [AtribuicaoController::class, 'create'])->name('atribuicoes.create');
    Route::post('/atribuir-aulas', [AtribuicaoController::class, 'store'])->name('atribuicoes.store');
    
    // Rota de Geração de Relatórios PDF
    Route::get('/relatorios/evasao', [RelatorioController::class, 'evasao'])->name('relatorios.evasao');

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
});

require __DIR__.'/auth.php';