<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendamentoRequest;
use App\Models\Espaco;
use App\Models\Agendamento;
use App\Models\HorarioReserva;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate(['tipo' => 'nullable|in:espaco,equipamento']);
        $tipo = $validated['tipo'] ?? null;
        $espacos = Espaco::where('status', true)
            ->when($tipo, fn ($query) => $query->where('tipo', $tipo))
            ->get();
        return view('agendamentos.index', compact('espacos', 'tipo'));
    }

    public function create(Espaco $espaco, AgendamentoRequest $request)
    {
        $dataSelecionada = $request->validated('data');
        [$dataMinima, $dataMaxima] = $request->periodoPermitido();
        
        $agendamentos = Agendamento::with('user')
            ->where('espaco_id', $espaco->id)
            ->where('data', $dataSelecionada)
            ->orderBy('horario_inicio')
            ->get();

        // RF11 - faixas de horário configuradas, disponíveis para seleção rápida na reserva.
        $horarios = HorarioReserva::where('ativo', true)->orderBy('horario_inicio')->get();

        return view('agendamentos.create', compact('espaco', 'dataSelecionada', 'agendamentos', 'horarios', 'dataMinima', 'dataMaxima'));
    }

    public function agenda(Request $request, Espaco $espaco)
    {
        $request->validate(['historico' => 'sometimes|boolean']);
        $historico = $request->boolean('historico');
        $agora = CarbonImmutable::now(config('app.timezone'));
        $operadorData = $historico ? '<' : '>';
        $operadorHora = $historico ? '<=' : '>';
        $ordem = $historico ? 'desc' : 'asc';

        $agendamentos = $espaco->agendamentos()->with('user')
            ->where(function ($query) use ($agora, $operadorData, $operadorHora) {
                $query->where('data', $operadorData, $agora->toDateString())
                    ->orWhere(function ($query) use ($agora, $operadorHora) {
                        $query->where('data', $agora->toDateString())
                            ->where('horario_fim', $operadorHora, $agora->format('H:i:s'));
                    });
            })
            ->orderBy('data', $ordem)->orderBy('horario_inicio')->orderBy('id')
            ->paginate(30)->withQueryString();
        $reservasPorDia = $agendamentos->getCollection()->groupBy('data');

        return view('agendamentos.agenda', compact('espaco', 'agendamentos', 'reservasPorDia', 'historico', 'agora'));
    }

    public function store(AgendamentoRequest $request, Espaco $espaco)
    {
        // Bloqueio de concorrência
        $conflict = Agendamento::where('espaco_id', $espaco->id)
            ->where('data', $request->data)
            ->where('horario_inicio', '<', $request->horario_fim)
            ->where('horario_fim', '>', $request->horario_inicio)
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', '⚠️ Este horário colide com uma reserva já existente.');
        }

        Agendamento::create([
            'espaco_id' => $espaco->id,
            'user_id' => Auth::id(),
            'data' => $request->data,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim' => $request->horario_fim,
            'motivo' => $request->motivo,
        ]);

        return redirect()->route('agendamentos.agenda', $espaco)
            ->with('success', '✅ Reserva efetuada com sucesso!');
    }

    public function edit(AgendamentoRequest $request, Agendamento $agendamento)
    {
        [$dataMinima, $dataMaxima] = $request->periodoPermitido();
        $agendamento->load(['espaco', 'user']);

        return view('agendamentos.edit', compact('agendamento', 'dataMinima', 'dataMaxima'));
    }

    public function update(AgendamentoRequest $request, Agendamento $agendamento)
    {
        $dados = $request->validated();
        $conflito = Agendamento::where('espaco_id', $agendamento->espaco_id)
            ->where('id', '!=', $agendamento->id)
            ->where('data', $dados['data'])
            ->where('horario_inicio', '<', $dados['horario_fim'])
            ->where('horario_fim', '>', $dados['horario_inicio'])
            ->exists();

        if ($conflito) {
            return redirect()->route('agendamentos.edit', $agendamento)
                ->withInput()->withErrors(['horario_inicio' => 'Este horário colide com uma reserva já existente.']);
        }

        $agendamento->update($dados);

        return redirect()->route('agendamentos.agenda', $agendamento->espaco_id)
            ->with('success', 'Reserva atualizada com sucesso!');
    }
}
