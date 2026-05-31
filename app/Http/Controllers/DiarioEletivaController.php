<?php

namespace App\Http\Controllers;

use App\Models\Eletiva;
use App\Models\FrequenciaEletiva;
use App\Models\NotaEletiva;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DiarioEletivaController extends Controller
{
    /**
     * Exibe a tela do diário da eletiva
     */
    public function show(Request $request, $id)
    {
        $eletiva = Eletiva::with(['alunosAtivos' => function ($query) {
            $query->orderBy('nome');
        }])->findOrFail($id);

        // Apenas o professor responsável, coordenador ou superior pode ver/editar
        if (!auth()->user()->hasRole(['Gestor', 'Secretaria', 'Coordenador']) && !$eletiva->professores->contains(auth()->id())) {
            abort(403, 'Acesso negado.');
        }

        $dataSelecionada = $request->input('data', Carbon::today()->format('Y-m-d'));
        $dataAvaliacao = $request->input('data_avaliacao', Carbon::today()->format('Y-m-d'));
        $descricaoAvaliacao = $request->input('descricao', '');
        
        // Carrega frequências já lançadas para a data (se houver)
        $frequencias = FrequenciaEletiva::where('eletiva_id', $eletiva->id)
            ->whereDate('data', $dataSelecionada)
            ->get()
            ->keyBy('aluno_id');

        // Carrega notas já lançadas na eletiva (para a listagem na aba notas)
        $notas = NotaEletiva::where('eletiva_id', $eletiva->id)
            ->orderBy('data', 'desc')
            ->get()
            ->groupBy('aluno_id');

        // Pega lista de descrições únicas de avaliações já feitas para facilitar
        $avaliacoes = NotaEletiva::where('eletiva_id', $eletiva->id)
            ->select('descricao', 'data')
            ->distinct()
            ->orderBy('data', 'desc')
            ->get();

        return view('eletivas.diario', compact('eletiva', 'dataSelecionada', 'dataAvaliacao', 'descricaoAvaliacao', 'frequencias', 'notas', 'avaliacoes'));
    }

    /**
     * Salva a frequência em lote para uma data
     */
    public function salvarFrequencia(Request $request, $id)
    {
        $eletiva = Eletiva::findOrFail($id);

        if (!auth()->user()->hasRole(['Gestor', 'Secretaria', 'Coordenador']) && !$eletiva->professores->contains(auth()->id())) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'data' => 'required|date',
            'frequencia' => 'required|array',
            'frequencia.*' => 'in:P,F,FJ'
        ]);

        $data = $request->data;
        $frequencias = $request->frequencia;

        DB::transaction(function () use ($eletiva, $data, $frequencias) {
            foreach ($frequencias as $aluno_id => $status) {
                FrequenciaEletiva::updateOrCreate(
                    [
                        'eletiva_id' => $eletiva->id,
                        'aluno_id' => $aluno_id,
                        'data' => $data,
                    ],
                    [
                        'user_id' => auth()->id(),
                        'status' => $status
                    ]
                );
            }
        });

        return redirect()->route('eletivas.diario.show', ['id' => $eletiva->id, 'data' => $data, 'tab' => 'frequencia'])
            ->with('success', 'Frequência salva com sucesso!');
    }

    /**
     * Salva as notas em lote para uma avaliação
     */
    public function salvarNota(Request $request, $id)
    {
        $eletiva = Eletiva::findOrFail($id);

        if (!auth()->user()->hasRole(['Gestor', 'Secretaria', 'Coordenador']) && !$eletiva->professores->contains(auth()->id())) {
            abort(403, 'Acesso negado.');
        }

        if (!$eletiva->usa_nota) {
            return back()->with('error', 'Esta eletiva não utiliza sistema de notas.');
        }

        $request->validate([
            'data_avaliacao' => 'required|date',
            'descricao' => 'required|string|max:255',
            'notas' => 'required|array',
            'notas.*' => ['nullable', 'numeric', 'min:0', 'max:100', 'regex:/^\d+(\.\d{1,2})?$/']
        ], [
            'notas.*.regex' => 'A nota informada não pode ter mais de 2 casas decimais.',
            'notas.*.min' => 'A nota não pode ser negativa.',
            'notas.*.max' => 'A nota não pode ultrapassar 100.'
        ]);

        $data = $request->data_avaliacao;
        $descricao = $request->descricao;
        $notas = $request->notas;

        DB::transaction(function () use ($eletiva, $data, $descricao, $notas) {
            foreach ($notas as $aluno_id => $notaValor) {
                // Se a nota for enviada vazia, ignora ou apaga se existir?
                // No caso mais simples, se estiver vazia e já existia, apaga.
                if ($notaValor === null || $notaValor === '') {
                    NotaEletiva::where('eletiva_id', $eletiva->id)
                        ->where('aluno_id', $aluno_id)
                        ->whereDate('data', $data)
                        ->where('descricao', $descricao)
                        ->delete();
                    continue;
                }

                NotaEletiva::updateOrCreate(
                    [
                        'eletiva_id' => $eletiva->id,
                        'aluno_id' => $aluno_id,
                        'data' => $data,
                        'descricao' => $descricao
                    ],
                    [
                        'nota' => $notaValor
                    ]
                );
            }
        });

        return redirect()->route('eletivas.diario.show', ['id' => $eletiva->id, 'tab' => 'notas'])
            ->with('success', 'Notas salvas com sucesso!');
    }
}
