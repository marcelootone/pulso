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
            // Normalizar o tipo de usuário usando constantes da model User
            $tipoIngresso = strtoupper($data['tipo_usuario']);
            
            $tipoUsuario = User::TIPO_ESTUDANTE; // Default
            if (in_array($tipoIngresso, ['ESTUDANTE', 'ALUNO'])) {
                $tipoUsuario = User::TIPO_ESTUDANTE;
            } elseif (in_array($tipoIngresso, ['PROFESSOR(A)', 'PROFESSOR', 'PROFESSORA'])) {
                $tipoUsuario = User::TIPO_PROFESSOR;
            } elseif ($tipoIngresso === 'GESTOR') {
                $tipoUsuario = User::TIPO_GESTOR;
            } elseif ($tipoIngresso === 'COORDENADOR') {
                $tipoUsuario = User::TIPO_COORDENADOR;
            } elseif ($tipoIngresso === 'SECRETARIA') {
                $tipoUsuario = User::TIPO_SECRETARIA;
            } elseif ($tipoIngresso === 'PROFESSOR EDUCAÇÃO ESPECIAL') {
                $tipoUsuario = User::TIPO_PROF_ESPECIAL;
            } elseif ($tipoIngresso === 'PROFESSOR DE ESTUDO ORIENTADO') {
                $tipoUsuario = User::TIPO_PROF_ESTUDO_ORIENTADO;
            } else {
                $tipoUsuario = $data['tipo_usuario']; // fallback para o valor original se não mapeado
            }

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
            if ($tipoUsuario === User::TIPO_ESTUDANTE) {
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
            if ($tipoUsuario === User::TIPO_ESTUDANTE) {
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
