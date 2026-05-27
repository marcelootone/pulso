<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FrequenciaService;
use App\Models\Turma;
use App\Models\BuscaAtivaRegistro;
use Carbon\Carbon;

class FrequenciaController extends Controller
{
    protected $frequenciaService;

    public function __construct(FrequenciaService $frequenciaService)
    {
        $this->frequenciaService = $frequenciaService;
    }

    /**
     * Exibe o painel do coordenador com o resumo de frequência por turma.
     */
    public function index(Request $request)
    {
        $mes = $request->input('mes', date('n'));
        $ano = $request->input('ano', date('Y'));

        $turmas = $this->frequenciaService->getResumoTurmas((int)$mes, (int)$ano);

        return view('frequencia.index', compact('turmas', 'mes', 'ano'));
    }

    /**
     * Exibe o formulário de monitoramento (lançamento de chamadas).
     */
    public function monitorar(Request $request)
    {
        $turmas = Turma::where('ativa', true)
            ->orderBy('modalidade')
            ->orderBy('serie')
            ->orderBy('complemento')
            ->get();
            
        $turmaSelecionada = $request->input('turma_id');
        $dataSelecionada = $request->input('data', date('Y-m-d'));
        
        $alunos = collect();
        if ($turmaSelecionada) {
            $alunos = $this->frequenciaService->getAlunosTurma((int)$turmaSelecionada, $dataSelecionada);
        }

        return view('frequencia.monitorar', compact('turmas', 'turmaSelecionada', 'dataSelecionada', 'alunos'));
    }

    /**
     * Salva as frequências lançadas no monitoramento.
     */
    public function store(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'data' => 'required|date',
            'frequencias' => 'required|array'
        ]);

        $this->frequenciaService->salvarFrequencia(
            $request->only(['turma_id', 'data', 'frequencias']), 
            auth()->id()
        );

        return redirect()->route('frequencia.monitorar', [
            'turma_id' => $request->turma_id,
            'data' => $request->data
        ])->with('success', 'Frequência salva com sucesso!');
    }

    /**
     * Exibe o painel de Busca Ativa (alunos com < 75% de frequência no mês).
     */
    public function buscaAtiva(Request $request)
    {
        $mes = $request->input('mes', date('n'));
        $ano = $request->input('ano', date('Y'));
        $turmaId = $request->input('turma_id');

        $turmas = Turma::where('ativa', true)->orderBy('serie')->orderBy('complemento')->get();
        
        $alunosRisco = collect();
        // Apenas buscar se foi filtrado ou se quiser exibir do mês inteiro logo de cara
        $alunosRisco = $this->frequenciaService->getBuscaAtiva((int)$mes, (int)$ano, $turmaId ? (int)$turmaId : null);

        return view('frequencia.busca_ativa', compact('alunosRisco', 'turmas', 'turmaId', 'mes', 'ano'));
    }

    /**
     * Registra uma ação de Busca Ativa para o aluno.
     */
    public function registrarBuscaAtiva(Request $request)
    {
        $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
            'observacao' => 'required|string|max:1000',
            'data' => 'required|date',
        ]);

        BuscaAtivaRegistro::create([
            'aluno_id' => $request->aluno_id,
            'user_id' => auth()->id(),
            'observacao' => $request->observacao,
            'data' => $request->data
        ]);

        return redirect()->back()->with('success', 'Registro de busca ativa salvo com sucesso!');
    }
}
