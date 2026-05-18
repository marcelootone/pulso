<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\User;
use Illuminate\Http\Request;

class AtribuicaoController extends Controller
{
    public function create()
    {
        // Pega os usuários que são tipos de Professor
        $professores = User::whereIn('tipo_usuario', [
            User::TIPO_PROFESSOR, 
            User::TIPO_PROF_ESPECIAL, 
            User::TIPO_PROF_ESTUDO_ORIENTADO
        ])->get();
        // Pega as turmas ativas
        $turmas = Turma::where('ativa', true)->get();
        
        return view('atribuicoes.create', compact('professores', 'turmas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'turma_id' => 'required|exists:turmas,id',
            'disciplina' => 'required|string|max:100',
        ]);

        $professor = User::findOrFail($request->user_id);
        
        // A mágica: attach() liga o professor à turma na tabela intermediária salvando a disciplina!
        try {
            $professor->turmas()->attach($request->turma_id, ['disciplina' => $request->disciplina]);
            return redirect()->back()->with('success', 'Professor atribuído à turma com sucesso!');
        } catch (\Exception $e) {
            // Se der erro (ex: tentar cadastrar a mesma aula duas vezes devido à trava "unique")
            return redirect()->back()->withErrors(['erro' => 'Este professor já está atribuído a esta turma com esta disciplina.']);
        }
    }
}