<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Envia o link de recuperação de senha para o e-mail informado.
     *
     * Retorna o status do Laravel Password broker (string constant).
     * Não revela se o e-mail está ou não cadastrado — a mensagem retornada
     * é sempre a mesma do ponto de vista do usuário final (user enumeration protection).
     */
    public function sendResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * Redefine a senha do usuário com o token fornecido.
     *
     * Retorna o status do Laravel Password broker (string constant).
     */
    public function resetPassword(array $credentials): string
    {
        return Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );
    }
}
