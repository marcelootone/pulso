<?php

namespace App\Http\Controllers;

use App\Services\PlanejamentoSemanalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanejamentoSemanalController extends Controller
{
    protected PlanejamentoSemanalService $service;

    public function __construct(PlanejamentoSemanalService $service)
    {
        $this->service = $service;
    }

    /**
     * Exibe o planejamento da semana atual ou da semana contendo a data informada.
     */
    public function index(Request $request)
    {
        $data = $request->has('data')
            ? Carbon::parse($request->input('data'))
            : Carbon::today();

        $planejamento = $this->service->obterOuCriarSemana(Auth::user(), $data);

        [$semanaAnterior] = $this->service->calcularSemana($data->copy()->subWeek());
        [$proximaSemana] = $this->service->calcularSemana($data->copy()->addWeek());

        $diasSemana = PlanejamentoSemanalService::DIAS_SEMANA;

        $diasLabels = [
            'SEGUNDA' => 'Segunda-feira',
            'TERCA' => 'Terça-feira',
            'QUARTA' => 'Quarta-feira',
            'QUINTA' => 'Quinta-feira',
            'SEXTA' => 'Sexta-feira',
        ];

        $andamentoOptions = [
            '' => '— Selecione —',
            'CONCLUIDO' => 'Concluído',
            'EM_ANDAMENTO' => 'Em Andamento',
            'NAO_CONCLUIDO' => 'Não Concluído',
        ];

        return view('planejamento.index', compact(
            'planejamento',
            'semanaAnterior',
            'proximaSemana',
            'diasSemana',
            'diasLabels',
            'andamentoOptions'
        ));
    }

    /**
     * Salva todas as alterações do formulário.
     */
    public function salvar(Request $request)
    {
        $request->validate([
            'planejamento_id' => 'required|integer|exists:planejamento_semanal,id',
            'horarios' => 'required|array',
            'horarios.*.horario_inicio' => 'required|date_format:H:i',
            'horarios.*.horario_fim' => 'required|date_format:H:i',
        ]);

        $planejamento = \App\Models\PlanejamentoSemanal::where('id', $request->input('planejamento_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->service->salvarAlteracoes($request->only('horarios'), $planejamento);

        return redirect()
            ->route('planejamento.index', ['data' => $planejamento->semana_inicio->toDateString()])
            ->with('success', 'Alterações salvas com sucesso!');
    }

    /**
     * Adiciona um novo horário ao planejamento.
     */
    public function adicionarHorario(Request $request)
    {
        $request->validate([
            'planejamento_id' => 'required|integer|exists:planejamento_semanal,id',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim' => 'required|date_format:H:i|after:horario_inicio',
        ]);

        $planejamento = \App\Models\PlanejamentoSemanal::where('id', $request->input('planejamento_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->service->adicionarHorario(
            $planejamento,
            $request->input('horario_inicio'),
            $request->input('horario_fim')
        );

        return redirect()
            ->route('planejamento.index', ['data' => $planejamento->semana_inicio->toDateString()])
            ->with('success', 'Horário adicionado com sucesso!');
    }

    /**
     * Remove um horário do planejamento.
     */
    public function removerHorario(int $id)
    {
        $removed = $this->service->removerHorario($id, Auth::id());

        if (!$removed) {
            return redirect()->back()->with('error', 'Horário não encontrado ou sem permissão.');
        }

        return redirect()->back()->with('success', 'Horário removido com sucesso!');
    }

    /**
     * Reordena os horários.
     */
    public function reordenar(Request $request)
    {
        $request->validate([
            'planejamento_id' => 'required|integer|exists:planejamento_semanal,id',
            'horarios' => 'required|array',
            'horarios.*' => 'integer|exists:planejamento_horarios,id',
        ]);

        $this->service->reordenarHorarios(
            $request->input('horarios'),
            $request->input('planejamento_id'),
            Auth::id()
        );

        return response()->json(['success' => true]);
    }
}
