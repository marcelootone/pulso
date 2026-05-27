<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\RelatorioService;

class RelatorioController extends Controller
{
    protected $relatorioService;

    public function __construct(RelatorioService $relatorioService)
    {
        $this->relatorioService = $relatorioService;
    }

    /**
     * Exibe a tela central de emissão de relatórios.
     */
    public function index()
    {
        $turmas = Turma::where('ativa', true)->orderBy('serie')->orderBy('complemento')->get();
        return view('relatorios.index', compact('turmas'));
    }

    /**
     * Relatório 1: Alerta de Evasão (Busca Ativa Geral)
     */
    public function evasao()
    {
        // 1. Busca os alunos com menos de 75% de frequência
        $alunosEmRisco = Aluno::select('alunos.nome', 'alunos.ra', 'turmas.serie', 'turmas.complemento')
            ->join('matriculas', 'alunos.id', '=', 'matriculas.aluno_id')
            ->join('enturmacoes', 'matriculas.id', '=', 'enturmacoes.matricula_id')
            ->join('turmas', 'enturmacoes.turma_id', '=', 'turmas.id')
            ->join('frequencias', 'alunos.id', '=', 'frequencias.aluno_id')
            ->selectRaw('COUNT(CASE WHEN frequencias.status = "P" THEN 1 END) * 100 / COUNT(frequencias.id) as percentual')
            ->groupBy('alunos.id', 'alunos.nome', 'alunos.ra', 'turmas.serie', 'turmas.complemento')
            ->having('percentual', '<', 75)
            ->orderBy('percentual', 'asc')
            ->get();

        $dataAtual = date('d/m/Y');

        $pdf = Pdf::loadView('relatorios.evasao', compact('alunosEmRisco', 'dataAtual'));

        return $pdf->download('alerta_evasao_sigae_' . date('Y_m_d') . '.pdf');
    }

    /**
     * Relatório 2: Frequência Mensal de uma Turma
     */
    public function frequenciaMensal(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'mes' => 'required|numeric|min:1|max:12',
            'ano' => 'required|numeric'
        ]);

        $dados = $this->relatorioService->frequenciaMensal($request->turma_id, $request->mes, $request->ano);
        
        $dados['mes'] = $request->mes;
        $dados['ano'] = $request->ano;
        $dados['dataEmissao'] = date('d/m/Y H:i:s');

        // Configura papel A4 em modo paisagem devido ao número de colunas (dias)
        $pdf = Pdf::loadView('relatorios.frequencia_mensal', $dados)->setPaper('a4', 'landscape');

        $nomeArquivo = "frequencia_turma_{$dados['turma']->id}_{$request->mes}_{$request->ano}.pdf";
        return $pdf->download($nomeArquivo);
    }

    /**
     * Relatório 3: Ranking de Turmas com Mais Faltas
     */
    public function turmasComMaisFaltas(Request $request)
    {
        $request->validate([
            'mes' => 'required|numeric|min:1|max:12',
            'ano' => 'required|numeric'
        ]);

        $ranking = $this->relatorioService->rankingTurmasFaltas($request->mes, $request->ano);
        
        $mes = $request->mes;
        $ano = $request->ano;
        $dataEmissao = date('d/m/Y H:i:s');

        $pdf = Pdf::loadView('relatorios.turmas_faltas', compact('ranking', 'mes', 'ano', 'dataEmissao'));

        return $pdf->download("ranking_faltas_{$mes}_{$ano}.pdf");
    }
}