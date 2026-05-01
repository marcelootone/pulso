<?php

namespace App\Http\Controllers;

use App\Models\Eletiva;
use App\Models\Aluno;
use App\Models\User;
use Illuminate\Http\Request;

class EletivaController extends Controller
{
    // Lista todas as eletivas criadas
    public function index()
    {
        $eletivas = Eletiva::with(['professor', 'alunos'])->get();
        $professores = User::where('tipo_usuario', 'Professor')->get();
        return view('eletivas.index', compact('eletivas', 'professores'));
    }

    // Salva uma nova eletiva no banco
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'vagas' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
        ]);

        Eletiva::create($request->all());
        return redirect()->route('eletivas.index')->with('success', 'Eletiva criada com sucesso!');
    }

    // A TELA DE GESTÃO: Mostra os detalhes e permite matricular alunos
    public function show($id)
    {
        $eletiva = Eletiva::with('alunos.turma')->findOrFail($id);
        
        // Pega todos os alunos da escola que AINDA NÃO estão nesta eletiva
        // Trazendo junto a turma original deles para facilitar a visualização da secretaria
        $alunosDisponiveis = Aluno::with('turma')
            ->whereNotIn('id', $eletiva->alunos->pluck('id'))
            ->orderBy('nome')
            ->get();

        $vagasOcupadas = $eletiva->alunos->count();
        $vagasRestantes = $eletiva->vagas - $vagasOcupadas;

        return view('eletivas.show', compact('eletiva', 'alunosDisponiveis', 'vagasOcupadas', 'vagasRestantes'));
    }

    // Ação do Botão "Matricular Aluno"
    public function matricular(Request $request, $id)
    {
        $request->validate(['aluno_id' => 'required|exists:alunos,id']);
        
        $eletiva = Eletiva::findOrFail($id);

        // Trava de segurança: Verifica se ainda há vagas
        if ($eletiva->alunos()->count() >= $eletiva->vagas) {
            return redirect()->back()->withErrors(['erro' => 'Não há mais vagas disponíveis nesta eletiva.']);
        }

        // attach() liga o aluno à eletiva na tabela intermediária (aluno_eletiva)
        $eletiva->alunos()->attach($request->aluno_id);

        return redirect()->back()->with('success', 'Estudante matriculado com sucesso na Eletiva!');
    }

    // Ação do Botão "Remover Matrícula"
    public function remover($id, $aluno_id)
    {
        $eletiva = Eletiva::findOrFail($id);
        // detach() corta a ligação na tabela intermediária
        $eletiva->alunos()->detach($aluno_id);

        return redirect()->back()->with('success', 'Matrícula cancelada com sucesso.');
    }

    // TELA DE EDIÇÃO
    public function edit($id)
    {
        $eletiva = Eletiva::findOrFail($id);
        $professores = User::where('tipo_usuario', 'Professor')->get();
        return view('eletivas.edit', compact('eletiva', 'professores'));
    }

    // SALVAR A EDIÇÃO
    public function update(Request $request, $id)
    {
        $eletiva = Eletiva::findOrFail($id);
        $vagasOcupadas = $eletiva->alunos()->count();

        $request->validate([
            'nome' => 'required|string|max:255',
            // A REGRA DE SEGURANÇA: Vagas não podem ser menores que os alunos já matriculados
            'vagas' => 'required|integer|min:' . max(1, $vagasOcupadas), 
            'user_id' => 'required|exists:users,id',
        ], [
            'vagas.min' => "Você não pode reduzir as vagas para menos de {$vagasOcupadas}, pois já existem alunos matriculados."
        ]);

        $eletiva->update($request->all());
        return redirect()->route('eletivas.index')->with('success', 'Eletiva atualizada com sucesso!');
    }

    // EXCLUIR A ELETIVA
    public function destroy($id)
    {
        $eletiva = Eletiva::findOrFail($id);
        $eletiva->delete(); // O cascade do banco limpa a tabela intermediária automaticamente
        return redirect()->route('eletivas.index')->with('success', 'Turma Eletiva excluída permanentemente!');
    }
}