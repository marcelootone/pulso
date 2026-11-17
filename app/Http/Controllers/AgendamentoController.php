<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendamentoRequest;
use App\Models\Espaco;
use App\Models\Agendamento;
use App\Models\HorarioReserva;
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

        return redirect()->route('agendamentos.create', ['espaco' => $espaco->id, 'data' => $request->data])
            ->with('success', '✅ Reserva efetuada com sucesso!');
    }
}
