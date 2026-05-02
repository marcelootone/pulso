<?php

namespace App\Http\Controllers;

use App\Models\Espaco;
use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    public function index()
    {
        $espacos = Espaco::where('status', true)->get();
        return view('agendamentos.index', compact('espacos'));
    }

    public function create(Espaco $espaco, Request $request)
    {
        $dataSelecionada = $request->get('data', date('Y-m-d'));
        
        $agendamentos = Agendamento::with('user')
            ->where('espaco_id', $espaco->id)
            ->where('data', $dataSelecionada)
            ->orderBy('horario_inicio')
            ->get();

        return view('agendamentos.create', compact('espaco', 'dataSelecionada', 'agendamentos'));
    }

    public function store(Request $request, Espaco $espaco)
    {
        $request->validate([
            'data' => 'required|date',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim' => 'required|date_format:H:i|after:horario_inicio',
            'motivo' => 'nullable|string|max:255',
        ]);

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
