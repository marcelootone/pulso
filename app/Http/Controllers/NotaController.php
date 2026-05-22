<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\Nota;
use App\Services\NotaService;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    protected NotaService $notaService;

    public function __construct(NotaService $notaService)
    {
        $this->notaService = $notaService;
    }

    public function create($avaliacao_id)
    {
        $avaliacao = Avaliacao::with('turma')->findOrFail($avaliacao_id);
        $turma = $avaliacao->turma;
        
        // Busca os alunos ordenados por nome via enturmações ativas
        $enturmacoes = $turma->enturmacoes()->where('status', 'Ativo')->with('matricula.aluno')->get();
        $alunos = $enturmacoes->pluck('matricula.aluno')->sortBy('nome');
        
        // Busca as notas já lançadas para esta avaliação
        $notasLancadas = Nota::where('avaliacao_id', $avaliacao_id)
            ->pluck('valor', 'aluno_id')
            ->toArray();

        return view('notas.create', compact('avaliacao', 'turma', 'alunos', 'notasLancadas'));
    }

    public function store(Request $request, $avaliacao_id)
    {
        $avaliacao = Avaliacao::findOrFail($avaliacao_id);

        $request->validate([
            'notas' => 'array',
            'notas.*' => 'nullable|numeric|min:0|max:' . $avaliacao->valor_maximo,
        ]);

        $notas = $request->input('notas', []);

        $this->notaService->salvarNotasDaAvaliacao($notas, $avaliacao_id);

        return redirect()->back()->with('success', 'Notas salvas com sucesso!');
    }
}
