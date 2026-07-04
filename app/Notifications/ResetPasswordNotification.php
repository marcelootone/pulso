<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Expiração do link em minutos (lida da config).
     */
    protected function expiresIn(): int
    {
        return (int) config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);
    }

    /**
     * Monta a mensagem de e-mail em português com template personalizado.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);
        $expiresIn = $this->expiresIn();

        return (new MailMessage())
            ->subject('Recuperação de Senha — SIGAE')
            ->greeting('Olá, ' . ($notifiable->name ?? 'usuário') . '!')
            ->line('Recebemos uma solicitação para redefinir a senha da sua conta no **SIGAE — Sistema Integrado de Gestão e Acompanhamento Escolar**.')
            ->line('Clique no botão abaixo para criar uma nova senha. Este link é válido por **' . $expiresIn . ' minutos**.')
            ->action('Redefinir Minha Senha', $resetUrl)
            ->line('Se você **não** solicitou a redefinição de senha, nenhuma ação é necessária. Sua senha permanece inalterada.')
            ->line('Por segurança, nunca compartilhe este link com ninguém.')
            ->salutation('Atenciosamente, Equipe SIGAE');
    }
}
