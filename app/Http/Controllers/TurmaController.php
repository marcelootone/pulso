<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurmaController extends Controller
{
    public function index()
    {
        // Busca todas as turmas no banco
        $turmas = Turma::all();
        
        // Retorna a tela 'index' passando as turmas para ela
        return view('turmas.index', compact('turmas'));
    }

    public function create()
    {
        // Apenas retorna a tela com o formulário
        return view('turmas.create');
    }
    
    // ... (as outras funções store, edit, update, destroy deixamos para o próximo passo)

    public function store(Request $request)
    {
        // 1. Valida se a Secretaria preencheu tudo certo
        $request->validate([
            'modalidade' => 'required|string',
            'turno' => 'required|string',
            'serie' => 'required|string',
            'complemento' => 'nullable|string|max:3',
        ]);

        // 2. Salva no banco de dados
        Turma::create([
            'modalidade' => $request->modalidade,
            'turno' => $request->turno,
            'serie' => $request->serie,
            'complemento' => strtoupper($request->complemento),
            'ativa' => true, // Toda turma nasce ativa
        ]);

        // 3. Redireciona de volta para a lista com mensagem de sucesso
        return redirect()->route('turmas.index')->with('success', 'Turma criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // O "with('alunos')" faz um Eager Loading, puxando a turma e a lista de alunos de uma vez só com alta performance
        $turma = Turma::with('alunos')->findOrFail($id);
        
        return view('turmas.show', compact('turma'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
