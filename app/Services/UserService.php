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
            
            // Atribui a Role do Spatie
            if (\Spatie\Permission\Models\Role::where('name', $tipoUsuario)->exists()) {
                $user->assignRole($tipoUsuario);
            }

            // Se for aluno, cria o registro correspondente
            if ($tipoUsuario === User::TIPO_ESTUDANTE) {
                $aluno = Aluno::create([
                    'user_id' => $user->id,
                    'ra' => $data['ra'],
                    'nome' => $data['nome'],
                    'nascimento' => $data['nascimento'] ?? null,
                    'sexo' => $data['sexo'] ?? null,
                    'telefone' => $data['telefone'] ?? null,
                    'nome_mae' => $data['nome_mae'] ?? null,
                    'telefone_responsavel' => $data['tel_mae'] ?? null,
                ]);

                // Se houver turma informada, cria a matrícula e a enturmação principal
                if (!empty($data['turma_id'])) {
                    $anoLetivo = date('Y');
                    
                    $matricula = \App\Models\Matricula::create([
                        'aluno_id' => $aluno->id,
                        'ano_letivo' => $anoLetivo,
                        'status' => 'Ativa',
                    ]);

                    \App\Models\Enturmacao::create([
                        'matricula_id' => $matricula->id,
                        'turma_id' => $data['turma_id'],
                        'tipo_vinculo' => 'REGULAR',
                        'data_entrada' => now(),
                        'status' => 'Ativo',
                    ]);
                }
            }

            return $user;
        });
    }

    /**
     * Atualiza um usuário existente.
     * 
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateUser(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::findOrFail($id);
            
            $tipoIngresso = strtoupper($data['tipo_usuario']);
            $tipoUsuario = User::TIPO_ESTUDANTE;
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
                $tipoUsuario = $data['tipo_usuario'];
            }

            $userData = [
                'name' => $data['nome'],
                'email' => $data['email'] ?? $user->email,
                'cpf' => $data['cpf'] ?? $user->cpf,
                'nascimento' => $data['nascimento'] ?? $user->nascimento,
                'sexo' => $data['sexo'] ?? $user->sexo,
                'telefone' => $data['telefone'] ?? $user->telefone,
                'cidade' => $data['cidade'] ?? $user->cidade,
                'rua' => $data['rua'] ?? $user->rua,
                'numero' => $data['numero'] ?? $user->numero,
                'bairro' => $data['bairro'] ?? $user->bairro,
                'tipo_usuario' => $tipoUsuario,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            if ($tipoUsuario === User::TIPO_ESTUDANTE && isset($data['ra'])) {
                $userData['ra'] = $data['ra'];
            }

            $user->update($userData);
            
            // Atualiza a Role do Spatie
            if (\Spatie\Permission\Models\Role::where('name', $tipoUsuario)->exists()) {
                $user->syncRoles([$tipoUsuario]);
            }

            if ($tipoUsuario === User::TIPO_ESTUDANTE && $user->aluno) {
                $user->aluno->update([
                    'ra' => $data['ra'] ?? $user->aluno->ra,
                    'nome' => $data['nome'],
                    'nascimento' => $data['nascimento'] ?? $user->aluno->nascimento,
                    'sexo' => $data['sexo'] ?? $user->aluno->sexo,
                    'telefone' => $data['telefone'] ?? $user->aluno->telefone,
                    'nome_mae' => $data['nome_mae'] ?? $user->aluno->nome_mae,
                    'telefone_responsavel' => $data['tel_mae'] ?? $user->aluno->telefone_responsavel,
                ]);
            }

            return $user;
        });
    }

    /**
     * Desativa um usuário, preservando-o no banco de dados.
     * 
     * @param int $id
     * @return void
     */
    public function deactivateUser(int $id)
    {
        $user = User::findOrFail($id);
        $novoEstado = !($user->ativo ?? true);
        $user->ativo = $novoEstado;
        $user->save();
    }
}
