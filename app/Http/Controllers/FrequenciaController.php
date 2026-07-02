<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FrequenciaService;
use App\Models\Turma;
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
            
        $eletivas = \App\Models\Eletiva::where('ativa', true)->where('tipo', 'eletiva')->orderBy('nome')->get();
        $clubes = \App\Models\Eletiva::where('ativa', true)->where('tipo', 'clube')->orderBy('nome')->get();

        $destinoSelecionado = $request->input('destino');
        $dataSelecionada = $request->input('data', date('Y-m-d'));
        
        $disciplinas = [];
        if ($destinoSelecionado) {
            $partes = explode('_', $destinoSelecionado);
            if (count($partes) === 2) {
                [$tipoDestino, $idDestino] = $partes;
                if ($tipoDestino === 'turma') {
                    $disciplinas = $this->frequenciaService->getFrequenciasMonitoramento((int)$idDestino, $dataSelecionada);
                } else {
                    $disciplinas = $this->frequenciaService->getFrequenciasEletivaMonitoramento((int)$idDestino, $dataSelecionada);
                }
            }
        }

        return view('frequencia.monitorar', compact('turmas', 'eletivas', 'clubes', 'destinoSelecionado', 'dataSelecionada', 'disciplinas'));
    }

    /**
     * Salva as frequências lançadas no monitoramento.
     */
    public function store(Request $request)
    {
        $request->validate([
            'destino' => 'required|string',
            'data' => 'required|date',
            'frequencias' => 'required|array',
            'professor_id' => 'required|exists:users,id'
        ]);

        $partes = explode('_', $request->destino);
        if (count($partes) !== 2) {
            return back()->with('error', 'Destino inválido.');
        }

        $tipoDestino = $partes[0];
        $idDestino = $partes[1];

        if ($tipoDestino === 'turma') {
            $this->frequenciaService->salvarFrequencia([
                'turma_id' => $idDestino,
                'data' => $request->data,
                'frequencias' => $request->frequencias
            ], $request->input('professor_id'));
        } else {
            $this->frequenciaService->salvarFrequenciaEletiva([
                'eletiva_id' => $idDestino,
                'data' => $request->data,
                'frequencias' => $request->frequencias
            ], $request->input('professor_id'));
        }

        return redirect()->route('frequencia.monitorar', [
            'destino' => $request->destino,
            'data' => $request->data
        ])->with('success', 'Frequência salva com sucesso!');
    }

    // O painel e o registro de Busca Ativa foram movidos para o
    // BuscaAtivaController dedicado (ver Figura 21 - Diagrama de Sequência).
}
