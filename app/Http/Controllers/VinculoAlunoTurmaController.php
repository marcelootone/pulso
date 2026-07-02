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
            'tipo_vinculo' => 'required|string|in:REGULAR,ELETIVA,REFORCO,AEE,DEPENDENCIA,ITINERARIO',
        ], [
            'aluno_id.required' => 'O campo Aluno é obrigatório.',
            'turma_id.required' => 'O campo Turma é obrigatório.',
            'tipo_vinculo.required' => 'O tipo de vínculo é obrigatório.',
            'tipo_vinculo.in' => 'O tipo de vínculo é inválido.',
        ]);

        $aluno = Aluno::findOrFail($request->aluno_id);
        $turma = Turma::findOrFail($request->turma_id);

        if (!$turma->ativa) {
            return back()->with('error', 'A turma selecionada não está ativa.');
        }

        $anoLetivo = $turma->ano_letivo ?? date('Y');

        // Pega ou cria a matrícula para o ano letivo
        $matricula = \App\Models\Matricula::firstOrCreate([
            'aluno_id' => $aluno->id,
            'ano_letivo' => $anoLetivo,
        ], [
            'status' => 'Ativa',
        ]);

        // Regra de negócio: Apenas 1 vínculo REGULAR ativo por período letivo
        if ($request->tipo_vinculo === 'REGULAR') {
            $existingRegular = \App\Models\Enturmacao::where('matricula_id', $matricula->id)
                ->where('tipo_vinculo', 'REGULAR')
                ->where('status', 'Ativo')
                ->first();

            if ($existingRegular && $existingRegular->turma_id != $turma->id) {
                return back()->with('error', 'O aluno já possui um vínculo REGULAR ativo neste ano letivo.');
            }
        }

        // Verifica se já está vinculado a essa turma
        $existingEnturmacao = \App\Models\Enturmacao::where('matricula_id', $matricula->id)
            ->where('turma_id', $turma->id)
            ->first();

        if ($existingEnturmacao) {
            if ($existingEnturmacao->status === 'Ativo') {
                return back()->with('error', 'O aluno já está vinculado a esta turma.');
            } else {
                // Se estava inativo, reativa o vínculo
                $existingEnturmacao->update([
                    'status' => 'Ativo',
                    'tipo_vinculo' => $request->tipo_vinculo,
                    'data_entrada' => now(),
                    'data_saida' => null
                ]);
            }
        } else {
            \App\Models\Enturmacao::create([
                'matricula_id' => $matricula->id,
                'turma_id' => $turma->id,
                'tipo_vinculo' => $request->tipo_vinculo,
                'data_entrada' => now(),
                'status' => 'Ativo',
            ]);
        }

        return redirect()->route('vinculo.create')
                         ->with('success', 'Aluno vinculado à turma com sucesso!');
    }

    /**
     * Vincula múltiplos alunos a uma turma (usada pela aba "Vincular Aluno" na tela de importação).
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'destino'  => 'required|string',
            'aluno_ids' => 'required|array|min:1',
            'aluno_ids.*' => 'exists:alunos,id',
        ], [
            'aluno_ids.required' => 'Selecione ao menos um aluno.',
            'aluno_ids.min'      => 'Selecione ao menos um aluno.',
        ]);

        $destino = $request->destino;
        $partes = explode('_', $destino);
        if (count($partes) !== 2) {
            return back()->with('error', 'Destino inválido.');
        }
        [$tipoDestino, $idDestino] = $partes;

        $anoLetivo = date('Y');
        $vinculados  = 0;
        $reativados  = 0;
        $duplicados  = 0;

        if ($tipoDestino === 'turma') {
            $turma = Turma::findOrFail($idDestino);
            if (!$turma->ativa) {
                return back()->with('error', 'A turma selecionada não está ativa.');
            }
            $anoLetivo = $turma->ano_letivo ?? $anoLetivo;
        } else {
            $eletiva = \App\Models\Eletiva::findOrFail($idDestino);
            if (!$eletiva->ativa) {
                return back()->with('error', 'O destino selecionado não está ativo.');
            }
            $anoLetivo = $eletiva->ano_letivo ?? $anoLetivo;
        }

        foreach ($request->aluno_ids as $alunoId) {
            $aluno = Aluno::find($alunoId);
            if (!$aluno) {
                continue;
            }

            if ($tipoDestino === 'turma') {
                $matricula = \App\Models\Matricula::firstOrCreate(
                    ['aluno_id' => $aluno->id, 'ano_letivo' => $anoLetivo],
                    ['status'   => 'Ativa']
                );

                $existing = \App\Models\Enturmacao::where('matricula_id', $matricula->id)
                    ->where('turma_id', $idDestino)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'Ativo') {
                        $duplicados++;
                    } else {
                        $existing->update([
                            'status'       => 'Ativo',
                            'tipo_vinculo' => 'REGULAR',
                            'data_entrada' => now(),
                            'data_saida'   => null,
                        ]);
                        $reativados++;
                    }
                } else {
                    \App\Models\Enturmacao::create([
                        'matricula_id' => $matricula->id,
                        'turma_id'     => $idDestino,
                        'tipo_vinculo' => 'REGULAR',
                        'data_entrada' => now(),
                        'status'       => 'Ativo',
                    ]);
                    $vinculados++;
                }
            } else {
                // Eletiva ou Clube
                $existing = \Illuminate\Support\Facades\DB::table('aluno_eletiva')
                    ->where('aluno_id', $aluno->id)
                    ->where('eletiva_id', $idDestino)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'Ativo') {
                        $duplicados++;
                    } else {
                        \Illuminate\Support\Facades\DB::table('aluno_eletiva')
                            ->where('id', $existing->id)
                            ->update([
                                'status' => 'Ativo',
                                'data_inscricao' => now(),
                                'data_saida' => null,
                                'updated_at' => now(),
                            ]);
                        $reativados++;
                    }
                } else {
                    \Illuminate\Support\Facades\DB::table('aluno_eletiva')->insert([
                        'aluno_id' => $aluno->id,
                        'eletiva_id' => $idDestino,
                        'status' => 'Ativo',
                        'data_inscricao' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $vinculados++;
                }
            }
        }

        $msg = "Operação concluída: {$vinculados} aluno(s) vinculado(s)";
        if ($reativados) $msg .= ", {$reativados} reativado(s)";
        if ($duplicados) $msg .= ", {$duplicados} já estava(m) vinculado(s) (ignorado(s))";
        $msg .= '.';

        return redirect()
            ->route('importar.index', ['destino' => $destino, 'tab' => 'vincular'])
            ->with('success', $msg);
    }
}
