<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstudoOrientadoSolicitacaoRequest;
use App\Models\EstudoOrientadoAtividade;
use App\Models\Turma;
use App\Models\User;
use App\Services\EstudoOrientadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstudoOrientadoController extends Controller
{
    public function __construct(private EstudoOrientadoService $service) {}

    // =========================================================================
    // FLUXO 1: SOLICITAÇÕES — Acessível por Professores regulares
    // =========================================================================

    /**
     * Lista as solicitações feitas pelo professor autenticado.
     * Para Gestor/Coordenador: lista todas.
     */
    public function indexSolicitacoes(Request $request)
    {
        $user = Auth::user();

        $query = EstudoOrientadoAtividade::with(['turma', 'solicitante'])
            ->orderByDesc('data_prevista');

        // Professor regular só vê suas próprias solicitações
        if ($user->hasRole('Professor') && !$user->hasAnyRole(['Gestor', 'Coordenador', 'Secretaria'])) {
            $query->where('professor_solicitante_id', $user->id);
        }

        // Filtros opcionais
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('turma_id')) {
            $query->where('turma_id', $request->turma_id);
        }

        $atividades = $query->paginate(15);
        $turmas = Turma::where('ativa', true)->orderBy('serie')->get();

        return view('estudo-orientado.solicitacoes.index', compact('atividades', 'turmas'));
    }

    /**
     * Formulário para criar nova solicitação.
     * O professor só pode solicitar para turmas nas quais está vinculado.
     */
    public function createSolicitacao()
    {
        $user = Auth::user();

        // Gestor/Coordenador vê todas as turmas ativas
        if ($user->hasAnyRole(['Gestor', 'Coordenador', 'Secretaria'])) {
            $turmas = Turma::where('ativa', true)->orderBy('serie')->get();
        } else {
            // Professor só pode solicitar para as turmas dele
            $turmas = $user->turmas()->where('ativa', true)->orderBy('serie')->get();
        }

        return view('estudo-orientado.solicitacoes.create', compact('turmas'));
    }

    /**
     * Persiste a nova solicitação de atividade.
     */
    public function storeSolicitacao(EstudoOrientadoSolicitacaoRequest $request)
    {
        $this->service->criarSolicitacao(array_merge(
            $request->validated(),
            ['professor_solicitante_id' => Auth::id()]
        ));

        return redirect()
            ->route('estudo-orientado.solicitacoes.index')
            ->with('success', 'Solicitação de Estudo Orientado criada com sucesso!');
    }

    /**
     * Exibe o resultado da avaliação para o professor solicitante.
     * Modo somente leitura.
     */
    public function showResultado(int $id)
    {
        $atividade = EstudoOrientadoAtividade::with(['turma.professores', 'solicitante', 'cumprimentos.aluno'])
            ->findOrFail($id);

        $user = Auth::user();
        if (!$user->hasAnyRole(['Gestor', 'Coordenador', 'Secretaria']) && $atividade->professor_solicitante_id !== $user->id) {
            abort(403, 'Você não tem permissão para ver este resultado.');
        }

        $alunos  = $this->service->alunosDaTurma($atividade->turma_id);
        $cumprimentosExistentes = $atividade->cumprimentos->keyBy('aluno_id');
        
        $somenteLeitura = true;

        return view('estudo-orientado.avaliacoes.avaliar', compact('atividade', 'alunos', 'cumprimentosExistentes', 'somenteLeitura'));
    }

    // =========================================================================
    // FLUXO 2: AVALIAÇÕES — Acessível pelo Professor de Estudo Orientado
    // =========================================================================

    /**
     * Painel de Avaliações de Estudo Orientado.
     *
     * - Gestor / Coordenador: veem TODAS as atividades (visão administrativa).
     * - Professor de EO: vê apenas as atividades das turmas às quais está vinculado.
     */
    public function indexAvaliacoes(Request $request)
    {
        $user = Auth::user();

        $query = EstudoOrientadoAtividade::with(['turma', 'solicitante', 'cumprimentos'])
            ->orderBy('status')
            ->orderByDesc('data_prevista');

        // Somente Professor de EO é filtrado por turma vinculada.
        // Gestor e Coordenador veem tudo.
        if (!$user->hasAnyRole(['Gestor', 'Coordenador'])) {
            $turmasIds = $user->turmas()->pluck('turmas.id');
            $query->whereIn('turma_id', $turmasIds);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $atividades = $query->paginate(15);

        return view('estudo-orientado.avaliacoes.index', compact('atividades'));
    }

    /**
     * Exibe o checklist de alunos para o Professor de EO avaliar uma atividade.
     */
    public function showAvaliacao(int $id)
    {
        $atividade = EstudoOrientadoAtividade::with(['turma', 'solicitante', 'cumprimentos.aluno'])
            ->findOrFail($id);

        // Garante que o professor de EO seja da turma da atividade
        $user = Auth::user();
        if (!$user->hasAnyRole(['Gestor', 'Coordenador']) &&
            !$user->turmas()->where('turmas.id', $atividade->turma_id)->exists()
        ) {
            abort(403, 'Você não tem permissão para avaliar esta atividade.');
        }

        $alunos  = $this->service->alunosDaTurma($atividade->turma_id);

        // Monta mapa de cumprimentos já registrados: [aluno_id => cumprimento]
        $cumprimentosExistentes = $atividade->cumprimentos->keyBy('aluno_id');
        
        $somenteLeitura = false;

        return view('estudo-orientado.avaliacoes.avaliar', compact('atividade', 'alunos', 'cumprimentosExistentes', 'somenteLeitura'));
    }

    /**
     * Persiste a avaliação em lote (checklist dos alunos).
     */
    public function storeAvaliacao(Request $request, int $id)
    {
        $atividade = EstudoOrientadoAtividade::findOrFail($id);

        $user = Auth::user();
        if (!$user->hasAnyRole(['Gestor', 'Coordenador']) &&
            !$user->turmas()->where('turmas.id', $atividade->turma_id)->exists()
        ) {
            abort(403);
        }

        $cumprimentos = $request->input('cumprimentos', []);
        $this->service->salvarAvaliacao($id, $cumprimentos);

        return redirect()
            ->route('estudo-orientado.avaliacoes.index')
            ->with('success', 'Avaliação salva com sucesso! A atividade foi marcada como Avaliada.');
    }
}
