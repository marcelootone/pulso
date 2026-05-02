<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\Avaliacao;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    public function index($turma_id, $disciplina)
    {
        $turma = Turma::findOrFail($turma_id);
        $avaliacoes = Avaliacao::where('turma_id', $turma_id)
            ->where('disciplina', $disciplina)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('avaliacoes.index', compact('turma', 'disciplina', 'avaliacoes'));
    }

    public function store(Request $request, $turma_id, $disciplina)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'periodo' => 'required|string|max:255',
            'valor_maximo' => 'required|numeric|min:0',
            'data' => 'nullable|date',
        ]);

        Avaliacao::create([
            'turma_id' => $turma_id,
            'disciplina' => $disciplina,
            'nome' => $request->nome,
            'periodo' => $request->periodo,
            'valor_maximo' => $request->valor_maximo,
            'data' => $request->data,
        ]);

        return redirect()->back()->with('success', 'Avaliação criada com sucesso!');
    }
}
