<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FrequenciaService;
use App\Models\Turma;
use App\Models\Matricula;
use App\Models\BuscaAtivaRegistro;

/**
 * Operar Busca Ativa / Registrar evasão.
 *
 * Controlador dedicado ao módulo de Busca Ativa, conforme o Diagrama de
 * Sequência do TCC (Figura 21). Delega o cálculo de infrequência por janelas
 * deslizantes ao FrequenciaService (Diretrizes da SEDU-ES 2026) e persiste os
 * registros de acompanhamento.
 */
class BuscaAtivaController extends Controller
{
    protected $frequenciaService;

    public function __construct(FrequenciaService $frequenciaService)
    {
        $this->frequenciaService = $frequenciaService;
    }

    /**
     * Painel de estudantes infrequentes identificados pelas janelas deslizantes
     * da SEDU-ES / Portaria 254-R (semanal, mensal, trimestral e anual).
     */
    public function index(Request $request)
    {
        $mes = $request->input('mes', date('n'));
        $ano = $request->input('ano', date('Y'));
        $turmaId = $request->input('turma_id');

        // Preserva o sentinela 'todos'; só converte para inteiro quando for número.
        $mesParam = $mes === 'todos' ? 'todos' : (int) $mes;
        $anoParam = $ano === 'todos' ? 'todos' : (int) $ano;

        $turmas = Turma::where('ativa', true)->orderBy('serie')->orderBy('complemento')->get();

        $alunosRisco = $this->frequenciaService->getBuscaAtiva(
            $mesParam,
            $anoParam,
            $turmaId ? (int) $turmaId : null
        );

        return view('frequencia.busca_ativa', compact('alunosRisco', 'turmas', 'turmaId', 'mes', 'ano'));
    }

    /**
     * Registra uma ação/contato de Busca Ativa para o aluno, vinculando-o à sua
     * matrícula ativa no ano letivo corrente (conforme DER do TCC).
     */
    public function registrar(Request $request)
    {
        $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
            'observacao' => 'required|string|max:1000',
            'data' => 'required|date',
        ]);

        $matriculaId = Matricula::where('aluno_id', $request->aluno_id)
            ->orderByDesc('ano_letivo')
            ->value('id');

        BuscaAtivaRegistro::create([
            'aluno_id' => $request->aluno_id,
            'matricula_id' => $matriculaId,
            'user_id' => auth()->id(),
            'observacao' => $request->observacao,
            'data' => $request->data,
        ]);

        return redirect()->back()->with('success', 'Registro de busca ativa salvo com sucesso!');
    }
}
