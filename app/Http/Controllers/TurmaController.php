<?php

namespace App\Http\Controllers;

use App\Http\Requests\TurmaRequest;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurmaController extends Controller
{
    /**
     * Lista todas as turmas com filtros opcionais.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole(['Gestor', 'Secretaria', 'Coordenador'])) {
            $query = Turma::with('professores')->withCount(['enturmacoes' => function ($query) {
                $query->where('status', 'Ativo');
            }]);
        } else {
            $query = $user->turmas()->with('professores')->withCount(['enturmacoes' => function ($query) {
                $query->where('status', 'Ativo');
            }]);
        }

        if ($request->filled('status')) {
            if ($request->status === 'ativas') {
                $query->where('ativa', true);
            } elseif ($request->status === 'inativas') {
                $query->where('ativa', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serie', 'like', "%{$search}%")
                  ->orWhere('complemento', 'like', "%{$search}%")
                  ->orWhere('turno', 'like', "%{$search}%")
                  ->orWhere('tipo', 'like', "%{$search}%")
                  ->orWhere('modalidade', 'like', "%{$search}%")
                  ->orWhereHas('enturmacoes.matricula.aluno', function($sq) use ($search) {
                      $sq->where('nome', 'like', "%{$search}%");
                  })
                  ->orWhereHas('professores', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $turmasPorModalidade = $query->orderBy('modalidade')
                                     ->orderBy('ano_letivo', 'desc')
                                     ->orderBy('serie')
                                     ->orderBy('complemento')
                                     ->get()
                                     ->groupBy('modalidade');

        return view('turmas.index', compact('turmasPorModalidade'));
    }

    /**
     * Exibe formulário de criação.
     */
    public function create()
    {
        return view('turmas.create');
    }

    /**
     * Persiste uma nova turma.
     */
    public function store(TurmaRequest $request)
    {
        Turma::create([
            'modalidade'  => $request->modalidade,
            'turno'       => $request->turno,
            'serie'       => $request->serie,
            'complemento' => $request->complemento ? strtoupper($request->complemento) : null,
            'ano_letivo'  => $request->ano_letivo ?? date('Y'),
            'tipo'        => $request->tipo ?? 'REGULAR',
            'ativa'       => true,
        ]);

        return redirect()->route('turmas.index')
            ->with('success', 'Turma criada com sucesso!');
    }

    /**
     * Exibe detalhes da turma com alunos matriculados.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $query = Turma::with([
            'enturmacoes' => function ($q) {
                $q->where('status', 'Ativo')->with('matricula.aluno');
            },
            'professores'
        ]);

        if (!$user->hasRole(['Gestor', 'Secretaria', 'Coordenador'])) {
            $query->whereHas('professores', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        $turma = $query->findOrFail($id);

        return view('turmas.show', compact('turma'));
    }

    /**
     * Exibe formulário de edição da turma.
     */
    public function edit(string $id)
    {
        $turma = Turma::findOrFail($id);

        return view('turmas.edit', compact('turma'));
    }

    /**
     * Atualiza os dados da turma.
     * Não permite alterar 'ativa' por aqui — use destroy() para desativar.
     */
    public function update(TurmaRequest $request, string $id)
    {
        $turma = Turma::findOrFail($id);

        $turma->update([
            'modalidade'  => $request->modalidade,
            'turno'       => $request->turno,
            'serie'       => $request->serie,
            'complemento' => $request->complemento ? strtoupper($request->complemento) : null,
            'ano_letivo'  => $request->ano_letivo ?? $turma->ano_letivo,
            'tipo'        => $request->tipo ?? $turma->tipo,
        ]);

        return redirect()->route('turmas.show', $turma->id)
            ->with('success', 'Turma atualizada com sucesso!');
    }

    /**
     * Desativa a turma (soft-disable).
     * Não exclui o registro para preservar histórico de frequências e notas.
     */
    public function destroy(string $id)
    {
        $turma = Turma::findOrFail($id);

        // Alterna o estado: se ativa, desativa; se inativa, reativa
        $novoEstado = !$turma->ativa;
        $turma->update(['ativa' => $novoEstado]);

        $mensagem = $novoEstado
            ? 'Turma reativada com sucesso!'
            : 'Turma desativada com sucesso! O histórico foi preservado.';

        return redirect()->route('turmas.index')->with('success', $mensagem);
    }
}
