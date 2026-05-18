<?php

namespace App\Services;

use App\Models\User;
use App\Models\Aluno;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    /**
     * Cria um novo usuário e, se for aluno, também cria o registro na tabela alunos.
     *
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Normalizar o tipo de usuário
            $tipoUsuario = ($data['tipo_usuario'] === 'ESTUDANTE' || $data['tipo_usuario'] === 'Aluno' || $data['tipo_usuario'] === 'Estudante') ? 'Estudante' : $data['tipo_usuario'];

            // Prepara os dados básicos do usuário
            $userData = [
                'name' => $data['nome'],
                'email' => $data['email'] ?? null,
                'cpf' => $data['cpf'] ?? null,
                'nascimento' => $data['nascimento'] ?? null,
                'sexo' => $data['sexo'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'cidade' => $data['cidade'] ?? null,
                'rua' => $data['rua'] ?? null,
                'numero' => $data['numero'] ?? null,
                'bairro' => $data['bairro'] ?? null,
                'tipo_usuario' => $tipoUsuario,
            ];

            // Tratamento específico para o perfil "Aluno"
            if ($tipoUsuario === 'Estudante') {
                $userData['ra'] = $data['ra'];
                // Alunos recebem uma senha padrão (ex: CPF, Data de Nascimento ou RA)
                $userData['password'] = Hash::make($data['ra']); 
            } else {
                // Outros perfis recebem as credenciais enviadas
                $userData['password'] = Hash::make($data['password']);
            }

            // Cria o usuário
            $user = User::create($userData);

            // Se for aluno, cria o registro correspondente
            if ($tipoUsuario === 'Estudante') {
                Aluno::create([
                    'user_id' => $user->id,
                    'turma_id' => $data['turma_id'] ?? null,
                    'ra' => $data['ra'],
                    'nome' => $data['nome'],
                    'nascimento' => $data['nascimento'] ?? null,
                    'sexo' => $data['sexo'] ?? null,
                    'telefone' => $data['telefone'] ?? null,
                    'nome_mae' => $data['nome_mae'] ?? null,
                    'telefone_responsavel' => $data['tel_mae'] ?? null,
                ]);
            }

            return $user;
        });
    }
}
