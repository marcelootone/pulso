<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnturmacaoController extends Controller
{
    public function index($turmaId)
    {
        // TODO: Implementar tela de enturmação
        return "Página de Enturmação da Turma {$turmaId} (Em desenvolvimento)";
    }

    public function store(\Illuminate\Http\Request $request, $turmaId)
    {
        // TODO: Implementar salvamento de enturmação
    }
}
