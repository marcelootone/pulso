<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // TODO: Validação dos dados (nome, email, tipo_usuario, etc)
        // TODO: Geração de senha padrão e criação do usuário no banco
        // TODO: Redirecionamento com mensagem de sucesso
        
        return redirect()->back()->with('success', 'Usuário criado com sucesso!');
    }
}
