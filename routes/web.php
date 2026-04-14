<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\ImportacaoController;
use App\Http\Controllers\AtribuicaoController;
use App\Http\Controllers\DiarioController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Grupo de rotas protegidas APENAS para funcionários
Route::middleware(['auth', 'restrito'])->group(function () {
    
    // O comando 'resource' cria magicamente as rotas /turmas, /turmas/create, etc.
    Route::resource('turmas', TurmaController::class);
    Route::get('/importar-alunos', [ImportacaoController::class, 'create'])->name('importar.create');
    Route::post('/importar-alunos', [ImportacaoController::class, 'store'])->name('importar.store');
    Route::get('/atribuir-aulas', [AtribuicaoController::class, 'create'])->name('atribuicoes.create');
    Route::post('/atribuir-aulas', [AtribuicaoController::class, 'store'])->name('atribuicoes.store');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/meu-diario', [DiarioController::class, 'index'])->name('diario.index');
    Route::get('/meu-diario/{id}', [DiarioController::class, 'show'])->name('diario.show');
    Route::post('/meu-diario/salvar', [DiarioController::class, 'store'])->name('diario.store');
});


require __DIR__.'/auth.php';
