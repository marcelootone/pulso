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

Route::middleware(['auth'])->group(function () {

    // ==========================================
    // MÓDULO 3.6: MATRIZ DE ACESSO GRANULAR
    // ==========================================

    // 1. FUNCIONÁRIOS E USUÁRIOS
    // Criar e Editar funcionários (Gestor, Secretaria)
    Route::group(['middleware' => ['role:Gestor|Secretaria']], function () {
        Route::get('users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}', [\App\Http\Controllers\UserController::class, 'update']);
    });

    // Visualizar funcionários (Gestor, Secretaria, Coordenador)
    Route::group(['middleware' => ['role:Gestor|Secretaria|Coordenador']], function () {
        Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    });

    // Excluir/Inativar funcionários (Apenas Gestor)
    Route::group(['middleware' => ['role:Gestor']], function () {
        Route::delete('users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });

    // 2. TURMAS E ESTUDANTES
    // Turmas - Criar/Editar/Excluir (Gestor, Secretaria, Coordenador)
    Route::group(['middleware' => ['role:Gestor|Secretaria|Coordenador']], function () {
        Route::get('turmas/create', [\App\Http\Controllers\TurmaController::class, 'create'])->name('turmas.create');
        Route::post('turmas', [\App\Http\Controllers\TurmaController::class, 'store'])->name('turmas.store');
        Route::get('turmas/{turma}/edit', [\App\Http\Controllers\TurmaController::class, 'edit'])->name('turmas.edit');
        Route::put('turmas/{turma}', [\App\Http\Controllers\TurmaController::class, 'update'])->name('turmas.update');
        Route::patch('turmas/{turma}', [\App\Http\Controllers\TurmaController::class, 'update']);
        Route::delete('turmas/{turma}', [\App\Http\Controllers\TurmaController::class, 'destroy'])->name('turmas.destroy');
    });

    // Turmas - Visualizar (Todos)
    Route::get('turmas', [\App\Http\Controllers\TurmaController::class, 'index'])->name('turmas.index');
    Route::get('turmas/{turma}', [\App\Http\Controllers\TurmaController::class, 'show'])->name('turmas.show');
    
    // Alunos - Visualizar (Gestor, Secretaria, Coordenador - assumindo que todos pedagógicos/admin podem ver)
    Route::group(['middleware' => ['role:Gestor|Secretaria|Coordenador']], function () {
        Route::get('alunos', [\App\Http\Controllers\AlunoController::class, 'index'])->name('alunos.index');
        Route::get('alunos/{aluno}', [\App\Http\Controllers\AlunoController::class, 'show'])->name('alunos.show');
    });



    // Alunos - Editar/Criar/Excluir e Importar (Gestor e Secretaria)
    Route::group(['middleware' => ['role:Gestor|Secretaria']], function () {
        Route::get('alunos/create', [\App\Http\Controllers\AlunoController::class, 'create'])->name('alunos.create');
        Route::post('alunos', [\App\Http\Controllers\AlunoController::class, 'store'])->name('alunos.store');
        Route::get('alunos/{aluno}/edit', [\App\Http\Controllers\AlunoController::class, 'edit'])->name('alunos.edit');
        Route::put('alunos/{aluno}', [\App\Http\Controllers\AlunoController::class, 'update'])->name('alunos.update');
        Route::patch('alunos/{aluno}', [\App\Http\Controllers\AlunoController::class, 'update']);
        Route::delete('alunos/{aluno}', [\App\Http\Controllers\AlunoController::class, 'destroy'])->name('alunos.destroy');

        // Importar Estudante
        Route::get('/importar-alunos', [ImportacaoController::class, 'index'])->name('importar.index');
        Route::post('/importar-alunos/preview', [ImportacaoController::class, 'preview'])->name('importar.preview');
        Route::post('/importar-alunos/confirmar', [ImportacaoController::class, 'confirm'])->name('importar.confirm');

        // Vincular aluno a turma
        Route::get('/vinculo-aluno-turma', [\App\Http\Controllers\VinculoAlunoTurmaController::class, 'create'])->name('vinculo.create');
        Route::post('/vinculo-aluno-turma', [\App\Http\Controllers\VinculoAlunoTurmaController::class, 'store'])->name('vinculo.store');
    });

    // 3. ATRIBUIÇÕES E ENTURMAÇÃO (Gestor, Secretaria, Coordenador)
    Route::group(['middleware' => ['role:Gestor|Secretaria|Coordenador']], function () {
        // Atribuir Aulas
        Route::get('/atribuir-aulas', [\App\Http\Controllers\AtribuicaoController::class, 'create'])->name('atribuicoes.create');
        Route::post('/atribuir-aulas', [\App\Http\Controllers\AtribuicaoController::class, 'store'])->name('atribuicoes.store');
        
        // Enturmações
        Route::get('/turmas/{turma}/enturmacao', [\App\Http\Controllers\EnturmacaoController::class, 'index'])->name('enturmacoes.index');
        Route::post('/turmas/{turma}/enturmacao', [\App\Http\Controllers\EnturmacaoController::class, 'store'])->name('enturmacoes.store');
    });

    // 4. FREQUÊNCIA E RELATÓRIOS (Gestor, Secretaria, Coordenador)
    Route::group(['middleware' => ['role:Gestor|Secretaria|Coordenador']], function () {
        Route::get('/frequencia', [\App\Http\Controllers\FrequenciaController::class, 'index'])->name('frequencia.index');
        Route::get('/frequencia/monitorar', [\App\Http\Controllers\FrequenciaController::class, 'monitorar'])->name('frequencia.monitorar');
        Route::post('/frequencia/monitorar', [\App\Http\Controllers\FrequenciaController::class, 'store'])->name('frequencia.store');
        Route::get('/frequencia/busca-ativa', [\App\Http\Controllers\FrequenciaController::class, 'buscaAtiva'])->name('frequencia.busca_ativa');
        Route::post('/frequencia/busca-ativa/registrar', [\App\Http\Controllers\FrequenciaController::class, 'registrarBuscaAtiva'])->name('frequencia.registrar_busca_ativa');

        Route::get('/relatorios', [\App\Http\Controllers\RelatorioController::class, 'index'])->name('relatorios.index');
        Route::get('/relatorio-evasao', [\App\Http\Controllers\RelatorioController::class, 'evasao'])->name('relatorios.evasao');
        Route::get('/relatorios/frequencia-mensal', [\App\Http\Controllers\RelatorioController::class, 'frequenciaMensal'])->name('relatorios.frequencia_mensal');
        Route::get('/relatorios/turmas-faltas', [\App\Http\Controllers\RelatorioController::class, 'turmasComMaisFaltas'])->name('relatorios.turmas_faltas');
    });

    // 5. ESPAÇOS E AGENDAMENTOS
    // Espaços - Criar/Editar/Excluir (Gestor, Secretaria)
    Route::group(['middleware' => ['role:Gestor|Secretaria']], function () {
        Route::get('espacos/create', [\App\Http\Controllers\EspacoController::class, 'create'])->name('espacos.create');
        Route::post('espacos', [\App\Http\Controllers\EspacoController::class, 'store'])->name('espacos.store');
        Route::get('espacos/{espaco}/edit', [\App\Http\Controllers\EspacoController::class, 'edit'])->name('espacos.edit');
        Route::put('espacos/{espaco}', [\App\Http\Controllers\EspacoController::class, 'update'])->name('espacos.update');
        Route::patch('espacos/{espaco}', [\App\Http\Controllers\EspacoController::class, 'update']);
        // Route::delete('espacos/{espaco}', [\App\Http\Controllers\EspacoController::class, 'destroy'])->name('espacos.destroy'); // Destroy estava excetuado antes
    });

    // Espaços - Visualizar e Agendamentos em si (Todos)
    Route::get('espacos', [\App\Http\Controllers\EspacoController::class, 'index'])->name('espacos.index');
    Route::get('/agendamentos', [\App\Http\Controllers\AgendamentoController::class, 'index'])->name('agendamentos.index');
    Route::get('/agendamentos/{espaco}/reservar', [\App\Http\Controllers\AgendamentoController::class, 'create'])->name('agendamentos.create');
    Route::post('/agendamentos/{espaco}/reservar', [\App\Http\Controllers\AgendamentoController::class, 'store'])->name('agendamentos.store');
    


    // 6. MEU DIÁRIO E PLANEJAMENTO SEMANAL (Professor, Coordenador, Gestor)
    Route::group(['middleware' => ['role:Gestor|Coordenador|Professor|Professor Educação Especial|Professor de Estudo Orientado']], function () {
        // Meu Diário
        Route::get('/meu-diario', [\App\Http\Controllers\DiarioController::class, 'index'])->name('diario.index');
        Route::get('/meu-diario/{id}', [\App\Http\Controllers\DiarioController::class, 'show'])->name('diario.show');
        Route::post('/meu-diario/salvar', [\App\Http\Controllers\DiarioController::class, 'store'])->name('diario.store');
        
        // Avaliações
        Route::get('/turmas/{turma}/disciplinas/{disciplina}/avaliacoes', [\App\Http\Controllers\AvaliacaoController::class, 'index'])->name('avaliacoes.index');
        Route::post('/turmas/{turma}/disciplinas/{disciplina}/avaliacoes', [\App\Http\Controllers\AvaliacaoController::class, 'store'])->name('avaliacoes.store');

        // Lançamento de Notas em Lote (por avaliação)
        Route::get('/avaliacoes/{avaliacao}/notas', [\App\Http\Controllers\NotaController::class, 'create'])->name('notas.create');
        Route::post('/avaliacoes/{avaliacao}/notas', [\App\Http\Controllers\NotaController::class, 'store'])->name('notas.store');

        // Planejamento Semanal
        Route::get('/planejamento-semanal', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'index'])->name('planejamento.index');
        Route::post('/planejamento-semanal/salvar', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'salvar'])->name('planejamento.salvar');
        Route::post('/planejamento-semanal/reordenar', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'reordenar'])->name('planejamento.reordenar');
        Route::post('/planejamento-semanal/horario', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'adicionarHorario'])->name('planejamento.adicionar-horario');
        Route::delete('/planejamento-semanal/horario/{id}', [\App\Http\Controllers\PlanejamentoSemanalController::class, 'removerHorario'])->name('planejamento.remover-horario');
    });

    // 7. ELETIVAS E CLUBES
    // Criar/Editar (Gestor, Secretaria, Coordenador)
    Route::group(['middleware' => ['role:Gestor|Secretaria|Coordenador']], function () {
        Route::get('eletivas/create', [\App\Http\Controllers\EletivaController::class, 'create'])->name('eletivas.create');
        Route::post('eletivas', [\App\Http\Controllers\EletivaController::class, 'store'])->name('eletivas.store');
        Route::get('eletivas/{eletiva}/edit', [\App\Http\Controllers\EletivaController::class, 'edit'])->name('eletivas.edit');
        Route::put('eletivas/{eletiva}', [\App\Http\Controllers\EletivaController::class, 'update'])->name('eletivas.update');

        // Inscrições
        Route::post('eletivas/{eletiva}/inscrever', [\App\Http\Controllers\InscricaoEletivaController::class, 'store'])->name('inscricao-eletiva.store');
        Route::delete('eletivas/{eletiva}/alunos/{aluno}', [\App\Http\Controllers\InscricaoEletivaController::class, 'destroy'])->name('inscricao-eletiva.destroy');
        Route::post('eletivas/trocar-clube', [\App\Http\Controllers\InscricaoEletivaController::class, 'trocar'])->name('inscricao-eletiva.trocar');
    });

    // Excluir/Desativar (Apenas Gestor)
    Route::group(['middleware' => ['role:Gestor']], function () {
        Route::delete('eletivas/{eletiva}', [\App\Http\Controllers\EletivaController::class, 'destroy'])->name('eletivas.destroy');
    });

    // Visualizar (Gestor, Secretaria, Coordenador, Professor)
    Route::group(['middleware' => ['role:Gestor|Secretaria|Coordenador|Professor|Professor Educação Especial|Professor de Estudo Orientado']], function () {
        Route::get('eletivas', [\App\Http\Controllers\EletivaController::class, 'index'])->name('eletivas.index');
        Route::get('eletivas/{eletiva}', [\App\Http\Controllers\EletivaController::class, 'show'])->name('eletivas.show');

        // Diário de Eletivas (Frequência e Notas)
        Route::get('eletivas/{id}/diario', [\App\Http\Controllers\DiarioEletivaController::class, 'show'])->name('eletivas.diario.show');
        Route::post('eletivas/{id}/frequencia', [\App\Http\Controllers\DiarioEletivaController::class, 'salvarFrequencia'])->name('eletivas.diario.frequencia');
        Route::post('eletivas/{id}/notas', [\App\Http\Controllers\DiarioEletivaController::class, 'salvarNota'])->name('eletivas.diario.notas');
    });

    // 8. ESTUDO ORIENTADO
    // Solicitações: Professores regulares criam atividades para o Prof. de EO aplicar
    Route::group(['middleware' => ['role:Gestor|Coordenador|Secretaria|Professor|Professor Educação Especial']], function () {
        Route::get('/estudo-orientado/solicitacoes', [\App\Http\Controllers\EstudoOrientadoController::class, 'indexSolicitacoes'])->name('estudo-orientado.solicitacoes.index');
        Route::get('/estudo-orientado/solicitacoes/nova', [\App\Http\Controllers\EstudoOrientadoController::class, 'createSolicitacao'])->name('estudo-orientado.solicitacoes.create');
        Route::post('/estudo-orientado/solicitacoes', [\App\Http\Controllers\EstudoOrientadoController::class, 'storeSolicitacao'])->name('estudo-orientado.solicitacoes.store');
        Route::get('/estudo-orientado/solicitacoes/{id}/resultado', [\App\Http\Controllers\EstudoOrientadoController::class, 'showResultado'])->name('estudo-orientado.solicitacoes.show');
    });

    // Avaliações: Professor de EO aplica e registra cumprimento dos alunos
    Route::group(['middleware' => ['role:Gestor|Coordenador|Professor de Estudo Orientado']], function () {
        Route::get('/estudo-orientado/avaliacoes', [\App\Http\Controllers\EstudoOrientadoController::class, 'indexAvaliacoes'])->name('estudo-orientado.avaliacoes.index');
        Route::get('/estudo-orientado/avaliacoes/{id}', [\App\Http\Controllers\EstudoOrientadoController::class, 'showAvaliacao'])->name('estudo-orientado.avaliacoes.show');
        Route::post('/estudo-orientado/avaliacoes/{id}', [\App\Http\Controllers\EstudoOrientadoController::class, 'storeAvaliacao'])->name('estudo-orientado.avaliacoes.store');
    });
});

require __DIR__.'/auth.php';