<?php

namespace App\Http\Controllers;

use App\Models\HorarioReserva;
use Illuminate\Http\Request;

/**
 * RF11 - Configurar e gerenciar horários para a reserva de espaços.
 * Protegido pela permissão "gerenciar horarios".
 */
class HorarioController extends Controller
{
    public function index()
    {
        $horarios = HorarioReserva::orderBy('horario_inicio')->get();
        return view('horarios.index', compact('horarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'nullable|string|max:255',
            'horario_inicio' => 'required|date_format:H:i',
            'horario_fim' => 'required|date_format:H:i|after:horario_inicio',
        ], [
            'horario_fim.after' => 'O horário de término deve ser posterior ao de início.',
        ]);

        $existe = HorarioReserva::where('horario_inicio', $request->horario_inicio)
            ->where('horario_fim', $request->horario_fim)
            ->exists();

        if ($existe) {
            return back()->withInput()->with('error', 'Já existe uma faixa de horário com esse intervalo.');
        }

        HorarioReserva::create([
            'nome' => $request->nome,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim' => $request->horario_fim,
            'ativo' => $request->has('ativo'),
        ]);

        return redirect()->route('horarios.index')->with('success', 'Faixa de horário cadastrada com sucesso!');
    }

    public function update(Request $request, HorarioReserva $horario)
    {
        $horario->update(['ativo' => $request->boolean('ativo')]);

        return redirect()->route('horarios.index')->with('success', 'Faixa de horário atualizada.');
    }

    public function destroy(HorarioReserva $horario)
    {
        $horario->delete();

        return redirect()->route('horarios.index')->with('success', 'Faixa de horário removida.');
    }
}
