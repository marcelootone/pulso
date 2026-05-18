<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create()
    {
        $turmas = Turma::where('ativa', true)->get();
        return view('users.create', compact('turmas'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Validação Comum
        $rules = [
            'tipo_usuario' => 'required|string',
            'cpf' => 'nullable|string|unique:users,cpf',
            'nome' => 'required|string|max:255',
            'nascimento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F',
            'telefone' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
        ];

        // Validação Dinâmica
        if ($request->tipo_usuario === 'ESTUDANTE' || $request->tipo_usuario === 'Aluno' || $request->tipo_usuario === 'Estudante') {
            $rules['ra'] = 'required|string|unique:users,ra';
            $rules['nascimento'] = 'required|date';
            $rules['sexo'] = 'required|in:M,F';
            $rules['telefone'] = 'required|string';
            $rules['nome_mae'] = 'nullable|string|max:255';
            $rules['turma_id'] = 'nullable|exists:turmas,id';
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:6';
        }

        $messages = [
            'required' => 'O campo :attribute é obrigatório.',
            'unique' => 'Este :attribute já está em uso.',
            'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'max' => 'O campo :attribute não pode exceder :max caracteres.',
            'exists' => 'A opção selecionada para :attribute é inválida.',
            'in' => 'O valor para :attribute é inválido.',
            'date' => 'O campo :attribute deve ser uma data válida.'
        ];

        // Mapeamento amigável dos nomes dos campos
        $attributes = [
            'nome' => 'Nome',
            'email' => 'E-mail',
            'cpf' => 'CPF',
            'password' => 'Senha',
            'ra' => 'RA',
            'turma_id' => 'Turma',
            'nome_mae' => 'Nome da Mãe',
        ];

        $validatedData = $request->validate($rules, $messages, $attributes);
        
        // Incluir todos os dados na requisição (incluindo endereço e filiação) para o service
        $data = $request->all();

        try {
            $this->userService->createUser($data);
            return redirect()->back()->with('success', 'Usuário cadastrado com sucesso!');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1048) { // Column cannot be null
                preg_match("/Column '(.*?)' cannot be null/", $e->getMessage(), $matches);
                $campo = $matches[1] ?? 'desconhecido';
                $campoTraduzido = $attributes[$campo] ?? $campo;
                return redirect()->back()->withInput()->with('error', "Faltou preencher um campo obrigatório para o sistema: {$campoTraduzido}.");
            }
            if ($errorCode == 1062) { // Duplicate entry
                return redirect()->back()->withInput()->with('error', "Erro: Já existe um cadastro com esse dado único no sistema.");
            }
            return redirect()->back()->withInput()->with('error', 'Erro no banco de dados: Contate o suporte técnico.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar: ' . $e->getMessage());
        }
    }
}
