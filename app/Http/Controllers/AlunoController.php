<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Enturmacao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlunoController extends Controller
{
    public function index(Request $request)
    {
        $query = Aluno::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhere('ra', 'like', "%{$search}%");
        }

        $alunos = $query->orderBy('nome')->paginate(15);
        
        return view('alunos.index', compact('alunos'));
    }
    public function edit($id)
    {
        $aluno = Aluno::findOrFail($id);

        // Busca a enturmação ativa do aluno para saber a turma atual
        $enturmacaoAtiva = Enturmacao::whereHas('matricula', function ($q) use ($aluno) {
                $q->where('aluno_id', $aluno->id);
            })
            ->where('status', 'Ativo')
            ->latest()
            ->first();

        $turmaId = $enturmacaoAtiva?->turma_id;

        return view('alunos.edit', compact('aluno', 'turmaId'));
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

        // Busca a turma atual via enturmação ativa para o redirect
        $enturmacaoAtiva = Enturmacao::whereHas('matricula', function ($q) use ($aluno) {
                $q->where('aluno_id', $aluno->id);
            })
            ->where('status', 'Ativo')
            ->latest()
            ->first();

        $turmaId = $enturmacaoAtiva?->turma_id;

        if ($turmaId) {
            return redirect()->route('turmas.show', $turmaId)->with('success', 'Aluno atualizado com sucesso!');
        }

        return redirect()->route('turmas.index')->with('success', 'Aluno atualizado com sucesso!');
    }
    public function destroy($id)
    {
        $aluno = Aluno::findOrFail($id);

        // Busca a enturmação ativa ANTES de desativar, para poder redirecionar
        $enturmacaoAtiva = Enturmacao::whereHas('matricula', function ($q) use ($aluno) {
                $q->where('aluno_id', $aluno->id);
            })
            ->where('status', 'Ativo')
            ->latest()
            ->first();

        $turmaId = $enturmacaoAtiva?->turma_id;

        // Desativa a enturmação em vez de apagar o aluno
        if ($enturmacaoAtiva) {
            $enturmacaoAtiva->update(['status' => 'Inativo', 'data_saida' => now()]);
        }

        if ($turmaId) {
            return redirect()->route('turmas.show', $turmaId)->with('success', 'Aluno removido da turma com sucesso!');
        }

        return redirect()->route('turmas.index')->with('success', 'Aluno removido da turma com sucesso!');
    }
}