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

    public function index(Request $request)
    {
        // Pega apenas usuários que NÃO são estudantes e NÃO são administradores (o admin do sistema)
        $query = \App\Models\User::whereNotIn('tipo_usuario', [
            \App\Models\User::TIPO_ESTUDANTE,
            \App\Models\User::TIPO_ADMINISTRADOR
        ]);

        if ($request->filled('tipo_usuario')) {
            $query->where('tipo_usuario', $request->tipo_usuario);
        }

        if ($request->filled('status')) {
            if ($request->status === 'ativos') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativos') {
                $query->where('ativo', false);
            }
        }

        $users = $query->orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        if ($user->tipo_usuario === \App\Models\User::TIPO_ESTUDANTE) {
            return redirect()->route('users.index')->with('error', 'Para visualizar estudantes, vá para a gestão de turmas.');
        }

        return view('users.show', compact('user'));
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
        $tipoIngresso = strtoupper($request->tipo_usuario);
        if (in_array($tipoIngresso, ['ESTUDANTE', 'ALUNO'])) {
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

    public function edit($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        if ($user->tipo_usuario === \App\Models\User::TIPO_ESTUDANTE) {
            return redirect()->route('users.index')->with('error', 'Para editar estudantes, vá para a gestão de turmas.');
        }

        $turmas = Turma::where('ativa', true)->get();
        return view('users.edit', compact('user', 'turmas'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'tipo_usuario' => 'required|string',
            'cpf' => 'nullable|string|unique:users,cpf,'.$id,
            'nome' => 'required|string|max:255',
            'nascimento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F',
            'telefone' => 'nullable|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6',
        ];

        $messages = [
            'required' => 'O campo :attribute é obrigatório.',
            'unique' => 'Este :attribute já está em uso.',
            'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'max' => 'O campo :attribute não pode exceder :max caracteres.',
            'in' => 'O valor para :attribute é inválido.',
            'date' => 'O campo :attribute deve ser uma data válida.'
        ];

        $attributes = [
            'nome' => 'Nome',
            'email' => 'E-mail',
            'cpf' => 'CPF',
            'password' => 'Senha',
        ];

        $validatedData = $request->validate($rules, $messages, $attributes);
        $data = $request->all();

        try {
            $this->userService->updateUser($id, $data);
            return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = \App\Models\User::findOrFail($id);
            $this->userService->deactivateUser($id);
            
            $mensagem = $user->ativo ? 'Usuário ativado com sucesso!' : 'Usuário desativado com sucesso!';
            return redirect()->route('users.index')->with('success', $mensagem);
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Erro ao alterar status: ' . $e->getMessage());
        }
    }
}
