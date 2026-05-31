<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InscricaoEletivaController extends Controller
{
    protected $eletivaService;

    public function __construct(\App\Services\EletivaService $eletivaService)
    {
        $this->eletivaService = $eletivaService;
    }

    public function store(\Illuminate\Http\Request $request, \App\Models\Eletiva $eletiva)
    {
        $request->validate([
            'aluno_ids' => 'required|array',
            'aluno_ids.*' => 'exists:alunos,id'
        ]);

        // Verificar vagas
        $vagasOcupadas = $eletiva->alunosAtivos->count();
        $novasVagas = count($request->aluno_ids);
        
        if (($vagasOcupadas + $novasVagas) > $eletiva->vagas) {
            return back()->with('error', 'Número de vagas excedido.');
        }

        $this->eletivaService->inscreverAlunos($eletiva, $request->aluno_ids);

        return back()->with('success', 'Alunos inscritos com sucesso!');
    }

    public function destroy(\App\Models\Eletiva $eletiva, \App\Models\Aluno $aluno)
    {
        $this->eletivaService->removerAluno($eletiva, $aluno->id);
        return back()->with('success', 'Aluno removido com sucesso!');
    }

    public function trocar(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
            'clube_origem_id' => 'required|exists:eletivas,id',
            'clube_destino_id' => 'required|exists:eletivas,id',
        ]);

        $origem = \App\Models\Eletiva::findOrFail($request->clube_origem_id);
        $destino = \App\Models\Eletiva::findOrFail($request->clube_destino_id);

        if ($destino->alunosAtivos->count() >= $destino->vagas) {
            return back()->with('error', 'O clube de destino não possui vagas disponíveis.');
        }

        $this->eletivaService->transferirAluno($request->aluno_id, $origem, $destino);

        return back()->with('success', 'Troca de clube realizada com sucesso!');
    }
}
