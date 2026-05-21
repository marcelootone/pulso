<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlunoController extends Controller
{
    public function edit($id)
    {
        $aluno = Aluno::findOrFail($id);
        return view('alunos.edit', compact('aluno'));
    }

    public function update(Request $request, $id)
    {
        $aluno = Aluno::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'ra' => ['required', 'string', 'max:255', Rule::unique('alunos')->ignore($aluno->id)],
            'nome_mae' => 'nullable|string|max:255',
            'telefone_responsavel' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:255',
            'logradouro' => 'nullable|string|max:255',
            'nascimento' => 'nullable|string|max:20',
            'sexo' => 'nullable|string|in:M,F,m,f',
            'telefone' => 'nullable|string|max:255',
            'status_matricula' => 'required|string|in:Ativo,Novato,Transferido,Evasão',
        ]);

        $aluno->update($request->all());

        return redirect()->route('turmas.show', $aluno->turma_id)->with('success', 'Aluno atualizado com sucesso!');
    }
    public function destroy($id)
    {
        $aluno = Aluno::findOrFail($id);
        $turma_id = $aluno->turma_id;
        
        $aluno->update(['turma_id' => null]);

        return redirect()->route('turmas.show', $turma_id)->with('success', 'Aluno removido da turma com sucesso!');
    }
}