<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\Request;

class VinculoAlunoTurmaController extends Controller
{
    /**
     * Exibe o formulário de vinculação.
     */
    public function create()
    {
        // Carrega alunos e turmas em ordem alfabética para os selects
        $alunos = Aluno::orderBy('nome')->get();
        
        // Vamos formatar o nome da turma para exibir no select
        $turmas = Turma::all()->sortBy(function($turma) {
            return $turma->serie . ' ' . $turma->complemento;
        });

        return view('vinculos.create', compact('alunos', 'turmas'));
    }

    /**
     * Salva a vinculação no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
            'turma_id' => 'required|exists:turmas,id',
        ], [
            'aluno_id.required' => 'O campo Aluno é obrigatório.',
            'turma_id.required' => 'O campo Turma é obrigatório.',
        ]);

        $aluno = Aluno::findOrFail($request->aluno_id);
        
        // Usa o syncWithoutDetaching para não remover vínculos anteriores
        // e não duplicar se já existir
        $aluno->turmas()->syncWithoutDetaching([$request->turma_id]);

        return redirect()->route('vinculo.create')
                         ->with('success', 'Aluno vinculado à turma com sucesso!');
    }
}
