<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventoAgendaRequest;
use App\Models\EventoAgenda;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $dados = $request->validate(['mes' => ['nullable', 'date_format:Y-m']]);
        $mes = CarbonImmutable::createFromFormat('!Y-m', $dados['mes'] ?? now()->format('Y-m'), config('app.timezone'));
        $inicio = $mes->startOfWeek(CarbonInterface::SUNDAY);
        $fim = $mes->endOfMonth()->endOfWeek(CarbonInterface::SATURDAY)->startOfDay();
        $eventos = EventoAgenda::visiveisPara($request->user())
            ->with('user:id,name')->where('inicio', '<', $fim->addDay())->where('fim', '>', $inicio)
            ->orderByDesc('dia_inteiro')->orderBy('inicio')->orderBy('id')->get();

        $eventosPorDia = [];
        foreach (CarbonPeriod::create($inicio, $fim) as $dia) {
            $eventosPorDia[$dia->toDateString()] = $eventos->filter(fn ($evento) => $evento->inicio->lt($dia->copy()->addDay()) && $evento->fim->gt($dia->copy()->startOfDay()));
        }

        $dadosEventos = $eventos->mapWithKeys(fn (EventoAgenda $evento) => [$evento->id => [
            'id' => $evento->id, 'titulo' => $evento->titulo,
            'descricao' => $evento->descricao ?? '', 'local' => $evento->local ?? '',
            'data_inicio' => $evento->inicio->toDateString(),
            'data_fim' => ($evento->dia_inteiro ? $evento->fim->subDay() : $evento->fim)->toDateString(),
            'hora_inicio' => $evento->inicio->format('H:i'), 'hora_fim' => $evento->fim->format('H:i'),
            'dia_inteiro' => $evento->dia_inteiro, 'compartilhado' => $evento->compartilhado,
            'autor' => $evento->user?->name ?? 'Usuário removido',
            'proprio' => (int) $evento->user_id === (int) $request->user()->id,
            'pode_editar' => $request->user()->can('update', $evento),
            'update_url' => route('agenda.eventos.update', $evento),
            'delete_url' => route('agenda.eventos.destroy', $evento),
        ]]);

        return view('agenda.index', compact('mes', 'eventosPorDia', 'dadosEventos'));
    }

    public function store(EventoAgendaRequest $request)
    {
        $evento = new EventoAgenda($request->dadosEvento());
        $evento->user()->associate($request->user());
        $evento->save();

        return redirect()->route('agenda.index', ['mes' => $evento->inicio->format('Y-m')])
            ->with('success', 'Evento criado com sucesso!');
    }

    public function update(EventoAgendaRequest $request, EventoAgenda $evento)
    {
        $evento->update($request->dadosEvento());

        return redirect()->route('agenda.index', ['mes' => $evento->inicio->format('Y-m')])
            ->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(EventoAgenda $evento)
    {
        $mes = $evento->inicio->format('Y-m');
        $evento->delete();

        return redirect()->route('agenda.index', ['mes' => $mes])
            ->with('success', 'Evento excluído com sucesso!');
    }
}
