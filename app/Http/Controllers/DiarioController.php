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
        $turma = Turma::with(['enturmacoes' => function($q) {
            $q->where('status', 'Ativo')->with('matricula.aluno');
        }])->findOrFail($id);
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

        // 3. Busca os conteúdos ministrados nesta turma nesta data
        $conteudosExistentes = \App\Models\ConteudoMinistrado::where('turma_id', $id)
            ->where('data', $dataSelecionada)
            ->get()
            ->keyBy('aula_numero');

        return view('diario.show', compact('turma', 'dataSelecionada', 'frequenciasExistentes', 'conteudosExistentes'));
    }

    // Salva a frequência em lote
    public function store(Request $request)
    {
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'data' => 'required|date',
            'presencas' => 'required|array', // Recebe RA => Status
            'conteudos' => 'nullable|array',
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

        if ($request->has('conteudos')) {
            foreach ($request->conteudos as $aula_numero => $descricao) {
                if (!empty(trim($descricao))) {
                    \App\Models\ConteudoMinistrado::updateOrCreate(
                        [
                            'turma_id' => $request->turma_id,
                            'data' => $request->data,
                            'aula_numero' => $aula_numero,
                        ],
                        [
                            'descricao' => $descricao,
                            'disciplina' => null,
                        ]
                    );
                } else {
                    \App\Models\ConteudoMinistrado::where('turma_id', $request->turma_id)
                        ->where('data', $request->data)
                        ->where('aula_numero', $aula_numero)
                        ->delete();
                }
            }
        }

        return redirect()->route('diario.index')->with('success', 'Chamada e conteúdo salvos com sucesso!');
    }
}