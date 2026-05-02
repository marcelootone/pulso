<?php

namespace App\Http\Controllers;

use App\Models\Espaco;
use Illuminate\Http\Request;

class EspacoController extends Controller
{
    public function index()
    {
        $espacos = Espaco::all();
        return view('espacos.index', compact('espacos'));
    }

    public function create()
    {
        return view('espacos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'capacidade' => 'nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        Espaco::create([
            'nome' => $request->nome,
            'capacidade' => $request->capacidade,
            'status' => $request->has('status') ? true : false,
        ]);

        return redirect()->route('espacos.index')->with('success', 'Espaço criado com sucesso!');
    }

    public function edit(Espaco $espaco)
    {
        return view('espacos.edit', compact('espaco'));
    }

    public function update(Request $request, Espaco $espaco)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'capacidade' => 'nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        $espaco->update([
            'nome' => $request->nome,
            'capacidade' => $request->capacidade,
            'status' => $request->has('status') ? true : false,
        ]);

        return redirect()->route('espacos.index')->with('success', 'Espaço atualizado com sucesso!');
    }
}
