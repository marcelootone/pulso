<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstudoOrientadoSolicitacaoRequest;
use App\Http\Requests\EstudoOrientadoAnaliseRequest;
use App\Http\Requests\EstudoOrientadoAtendimentoRequest;
use App\Http\Requests\EstudoOrientadoEvolucaoRequest;
use App\Http\Requests\EstudoOrientadoPlanoAcaoRequest;
use App\Models\EstudoOrientadoSolicitacao;
use App\Models\Turma;
use App\Models\Aluno;
use App\Models\User;
use App\Services\EstudoOrientadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EstudoOrientadoController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private EstudoOrientadoService $service) {}

    // =========================================================================
    // SOLICITAÇÕES — Professor e Gestão
    // =========================================================================

    public function indexSolicitacoes(Request $request)
    {
        $user = Auth::user();
        
        $query = EstudoOrientadoSolicitacao::with(['turma', 'aluno', 'solicitante'])
            ->orderByDesc('created_at');

        // Se não for da gestão/coordenação, vê apenas as suas
        if (!$user->hasPermissionTo('consultar estudo orientado') && !$user->hasPermissionTo('analisar solicitacao estudo orientado')) {
            $query->where('professor_solicitante_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('aluno_id')) {
            $query->where('aluno_id', $request->aluno_id);
        }

        $solicitacoes = $query->paginate(15);
        
        // Alunos para filtro
        $alunos = Aluno::orderBy('nome')->get(); // Ideal seria filtrar por ativos ou turmas do professor

        return view('estudo-orientado.solicitacoes.index', compact('solicitacoes', 'alunos'));
    }

    public function createSolicitacao(Request $request)
    {
        $this->authorize('criarSolicitacao', EstudoOrientadoSolicitacao::class);
        $user = Auth::user();

        // Professor só pode solicitar para as turmas dele
        if ($user->hasPermissionTo('consultar estudo orientado')) {
            $turmas = Turma::where('ativa', true)->orderBy('serie')->get();
        } else {
            $turmas = $user->turmas()->where('ativa', true)->orderBy('serie')->get();
        }

        return view('estudo-orientado.solicitacoes.create', compact('turmas'));
    }

    public function getAlunosPorTurma($turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $alunoIds = $turma->enturmacoes()->where('status', 'Ativo')->pluck('matricula_id');
        $alunos = Aluno::whereHas('matriculas', function ($q) use ($alunoIds) {
            $q->whereIn('id', $alunoIds);
        })->orderBy('nome')->get(['id', 'nome']);

        return response()->json($alunos);
    }

    public function storeSolicitacao(EstudoOrientadoSolicitacaoRequest $request)
    {
        $this->authorize('criarSolicitacao', EstudoOrientadoSolicitacao::class);

        $this->service->criarSolicitacao(array_merge(
            $request->validated(),
            ['professor_solicitante_id' => Auth::id()]
        ));

        return redirect()
            ->route('estudo-orientado.solicitacoes.index')
            ->with('success', 'Solicitação de encaminhamento criada com sucesso!');
    }

    public function showSolicitacao($id)
    {
        $solicitacao = EstudoOrientadoSolicitacao::with(['turma', 'aluno', 'solicitante', 'historicos.user'])->findOrFail($id);
        $this->authorize('verSolicitacao', $solicitacao);

        return view('estudo-orientado.solicitacoes.show', compact('solicitacao'));
    }

    // =========================================================================
    // ANÁLISES — Coordenador
    // =========================================================================

    public function indexAnalises(Request $request)
    {
        $this->authorize('analisar', EstudoOrientadoSolicitacao::class);

        $query = EstudoOrientadoSolicitacao::with(['turma', 'aluno', 'solicitante'])
            ->orderBy('status')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $solicitacoes = $query->paginate(15);
        return view('estudo-orientado.analises.index', compact('solicitacoes'));
    }

    public function showAnalise($id)
    {
        $solicitacao = EstudoOrientadoSolicitacao::with(['turma', 'aluno', 'solicitante', 'historicos.user'])->findOrFail($id);
        
        // Verifica permissão para analisar ou consultar
        if (!Auth::user()->can('analisar', EstudoOrientadoSolicitacao::class) && !Auth::user()->can('consultar', EstudoOrientadoSolicitacao::class)) {
            $this->authorize('verSolicitacao', $solicitacao);
        }

        $orientadores = User::role(User::TIPO_PROF_ESTUDO_ORIENTADO)->get();
        
        return view('estudo-orientado.analises.show', compact('solicitacao', 'orientadores'));
    }

    public function storeAnalise(EstudoOrientadoAnaliseRequest $request, $id)
    {
        $solicitacao = EstudoOrientadoSolicitacao::findOrFail($id);
        $acao = $request->acao;

        if ($acao === 'aprovar') {
            $this->authorize('aprovar', $solicitacao);
            $this->service->aprovarSolicitacao($id, Auth::id(), $request->parecer);
            $msg = 'Solicitação aprovada com sucesso.';
        } elseif ($acao === 'rejeitar') {
            $this->authorize('rejeitar', $solicitacao);
            $this->service->rejeitarSolicitacao($id, Auth::id(), $request->parecer);
            $msg = 'Solicitação rejeitada.';
        } elseif ($acao === 'atribuir') {
            $this->authorize('atribuirOrientador', $solicitacao);
            $this->service->atribuirOrientador($id, Auth::id(), $request->professor_orientador_id);
            $msg = 'Orientador atribuído com sucesso.';
        }

        return redirect()->back()->with('success', $msg);
    }

    // =========================================================================
    // ACOMPANHAMENTOS — Professor Orientador
    // =========================================================================

    public function indexAcompanhamentos(Request $request)
    {
        $user = Auth::user();
        
        $query = EstudoOrientadoSolicitacao::with(['turma', 'aluno'])
            ->whereIn('status', ['Aprovada', 'EmAtendimento', 'Concluida']);

        // Se for orientador, vê apenas os dele
        if (!$user->can('analisar', EstudoOrientadoSolicitacao::class) && !$user->can('consultar', EstudoOrientadoSolicitacao::class)) {
            $query->where('professor_orientador_id', $user->id);
        }

        $acompanhamentos = $query->orderByDesc('updated_at')->paginate(15);
        return view('estudo-orientado.acompanhamentos.index', compact('acompanhamentos'));
    }

    public function showAcompanhamento($id)
    {
        $solicitacao = EstudoOrientadoSolicitacao::with([
            'aluno', 'turma', 'atendimentos', 'evolucoes', 'planosAcao', 'historicos.user'
        ])->findOrFail($id);

        $this->authorize('acompanhar', $solicitacao);

        return view('estudo-orientado.acompanhamentos.show', compact('solicitacao'));
    }

    public function storeAtendimento(EstudoOrientadoAtendimentoRequest $request, $id)
    {
        $solicitacao = EstudoOrientadoSolicitacao::findOrFail($id);
        $this->authorize('registrarAtendimento', $solicitacao);

        $this->service->registrarAtendimento($id, Auth::id(), $request->validated());

        return redirect()->back()->with('success', 'Atendimento registrado com sucesso.');
    }

    public function storeEvolucao(EstudoOrientadoEvolucaoRequest $request, $id)
    {
        $solicitacao = EstudoOrientadoSolicitacao::findOrFail($id);
        $this->authorize('registrarEvolucao', $solicitacao);

        $this->service->registrarEvolucao($id, Auth::id(), $request->validated());

        return redirect()->back()->with('success', 'Evolução registrada com sucesso.');
    }

    public function storePlanoAcao(EstudoOrientadoPlanoAcaoRequest $request, $id)
    {
        $solicitacao = EstudoOrientadoSolicitacao::findOrFail($id);
        $this->authorize('criarPlanoAcao', $solicitacao);

        $this->service->salvarPlanoAcao($id, Auth::id(), $request->validated());

        return redirect()->back()->with('success', 'Plano de ação salvo com sucesso.');
    }

    public function concluirAcompanhamento(Request $request, $id)
    {
        $solicitacao = EstudoOrientadoSolicitacao::findOrFail($id);
        $this->authorize('concluir', $solicitacao);

        $request->validate(['parecer_conclusao' => 'required|string|min:10']);

        $this->service->concluirAcompanhamento($id, Auth::id(), $request->parecer_conclusao);

        return redirect()->back()->with('success', 'Acompanhamento concluído com sucesso.');
    }
    public function relatorios(Request $request)
    {
        $this->authorize('consultar', EstudoOrientadoSolicitacao::class);

        $stats = [
            'total' => EstudoOrientadoSolicitacao::count(),
            'pendentes' => EstudoOrientadoSolicitacao::where('status', 'Pendente')->count(),
            'em_atendimento' => EstudoOrientadoSolicitacao::where('status', 'EmAtendimento')->count(),
            'concluidas' => EstudoOrientadoSolicitacao::where('status', 'Concluida')->count(),
        ];

        return view('estudo-orientado.relatorios.index', compact('stats'));
    }
}
