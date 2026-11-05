<?php

namespace App\Http\Controllers;

use App\Models\Espaco;
use Illuminate\Http\Request;

class EspacoController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate(['tipo' => 'nullable|in:espaco,equipamento']);
        $tipo = $validated['tipo'] ?? null;
        $espacos = Espaco::query()
            ->when($tipo, fn ($query) => $query->where('tipo', $tipo))
            ->get();
        return view('espacos.index', compact('espacos', 'tipo'));
    }

    public function create()
    {
        return view('espacos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:espaco,equipamento',
            'capacidade' => 'exclude_unless:tipo,espaco|nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        Espaco::create([
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'capacidade' => $request->tipo === 'espaco'
                ? $request->input('capacidade')
                : null,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('espacos.index')->with('success', 'Cadastro criado com sucesso!');
    }

    public function edit(Espaco $espaco)
    {
        return view('espacos.edit', compact('espaco'));
    }

    public function update(Request $request, Espaco $espaco)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:espaco,equipamento',
            'capacidade' => 'exclude_unless:tipo,espaco|nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        $espaco->update([
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'capacidade' => $request->tipo === 'espaco'
                ? $request->input('capacidade')
                : null,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('espacos.index')->with('success', 'Cadastro atualizado com sucesso!');
    }
}
