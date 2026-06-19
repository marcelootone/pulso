<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Enturmacao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\AlunoService;

class AlunoController extends Controller
{
    protected $alunoService;

    public function __construct(AlunoService $alunoService)
    {
        $this->alunoService = $alunoService;
    }

    public function index(Request $request)
    {
        $alunos = $this->alunoService->getAlunosPaginados(15, $request->search);
        
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
        $oldStatus = $aluno->status_matricula;

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
            'status_matricula' => 'required|string|in:Ativo,Novato,Transferência,Deixou de frequentar,Falecimento',
        ]);

        $aluno->update($request->all());

        // Se mudou para Deixou de frequentar, registrar na Busca Ativa
        if ($oldStatus !== $aluno->status_matricula && $aluno->status_matricula === 'Deixou de frequentar') {
            \App\Models\BuscaAtivaRegistro::create([
                'aluno_id' => $aluno->id,
                'user_id' => auth()->id() ?? 1,
                'observacao' => 'Status do aluno alterado para Deixou de frequentar (Alerta de evasão/abandono gerado automaticamente).',
                'data' => now()->format('Y-m-d')
            ]);
        }

        // Atualizar o status da matrícula correspondente no banco (ano letivo atual)
        if ($oldStatus !== $aluno->status_matricula) {
            $matriculaAtiva = \App\Models\Matricula::where('aluno_id', $aluno->id)
                ->where('ano_letivo', date('Y'))
                ->latest()
                ->first();
                
            if ($matriculaAtiva) {
                $matriculaAtiva->update(['status' => $aluno->status_matricula]);
            }
        }

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