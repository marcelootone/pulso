<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userData = [
                'name' => $data['nome'],
                'email' => $data['email'],
                'cpf' => $data['cpf'] ?? null,
                'nascimento' => $data['nascimento'] ?? null,
                'sexo' => $data['sexo'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'cidade' => $data['cidade'] ?? null,
                'rua' => $data['rua'] ?? null,
                'numero' => $data['numero'] ?? null,
                'bairro' => $data['bairro'] ?? null,
                'tipo_usuario' => $data['tipo_usuario'],
                'password' => Hash::make($data['password']),
            ];

            $user = User::create($userData);
            if (\Spatie\Permission\Models\Role::where('name', $data['tipo_usuario'])->exists()) {
                $user->assignRole($data['tipo_usuario']);
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
                'tipo_usuario' => $data['tipo_usuario'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $user->update($userData);
            
            // Atualiza a Role do Spatie
            if (\Spatie\Permission\Models\Role::where('name', $data['tipo_usuario'])->exists()) {
                $user->syncRoles([$data['tipo_usuario']]);
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
