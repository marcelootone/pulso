<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; // Importação da biblioteca Excel
use App\Imports\AlunosImport;        // Importação da classe que criamos

class ImportacaoController extends Controller
{
    public function create()
    {
        // Busca apenas as turmas ativas para mostrar no Dropdown
        $turmas = Turma::where('ativa', true)->get();
        return view('importacao.create', compact('turmas'));
    }

    public function store(Request $request)
    {
        // 1. Valida se a turma foi escolhida e se o arquivo possui uma extensão permitida
        $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'arquivo_csv' => 'required|file|extensions:csv,txt,xlsx,xls|max:5120',
        ]);

        // 2. A Mágica: Delega a leitura e inserção para a classe AlunosImport
        Excel::import(new AlunosImport($request->turma_id), $request->file('arquivo_csv'));

        // 3. Devolve para a tela com a mensagem de sucesso
        return redirect()->back()->with('success', "Estudantes importados com sucesso para a turma!");
    }
}