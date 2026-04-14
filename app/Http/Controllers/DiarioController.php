<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\Frequencia;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiarioController extends Controller
{
    public function index()
    {
        $professor = Auth::user();
        $minhasTurmas = $professor->turmas()->where('ativa', true)->get();
        return view('diario.index', compact('minhasTurmas'));
    }

    public function show(Request $request, $id)
    {
        $turma = Turma::with('alunos')->findOrFail($id);
        
        // 1. Pega a data da URL ou usa a data de hoje
        $dataSelecionada = $request->get('data', date('Y-m-d'));

        if (!Auth::user()->turmas->contains($id)) {
            abort(403);
        }

        // 2. Busca as frequências já registradas para esta turma nesta data
        $frequenciasExistentes = Frequencia::where('turma_id', $id)
            ->where('data', $dataSelecionada)
            ->get()
            ->keyBy('aluno_id'); // Organiza por ID do aluno para facilitar a busca na View

        return view('diario.show', compact('turma', 'dataSelecionada', 'frequenciasExistentes'));
    }

    // Salva a frequência em lote
    public function store(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'data' => 'required|date',
            'presencas' => 'required|array', // Recebe RA => Status
        ]);

        foreach ($request->presencas as $aluno_id => $status) {
            Frequencia::updateOrCreate(
                [
                    'aluno_id' => $aluno_id,
                    'turma_id' => $request->turma_id,
                    'data'     => $request->data,
                ],
                [
                    'user_id' => Auth::id(),
                    'status'  => $status,
                ]
            );
        }

        return redirect()->route('diario.index')->with('success', 'Chamada realizada com sucesso!');
    }
}