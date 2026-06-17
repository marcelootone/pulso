<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\EstudoOrientadoSolicitacao;

class EstudoOrientadoEncaminhadaNotification extends Notification
{
    use Queueable;

    private $solicitacao;

    /**
     * Create a new notification instance.
     */
    public function __construct(EstudoOrientadoSolicitacao $solicitacao)
    {
        $this->solicitacao = $solicitacao;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'mensagem' => 'Você foi designado como orientador para o aluno ' . $this->solicitacao->aluno->nome,
            'url' => route('estudo-orientado.acompanhamentos.show', $this->solicitacao->id),
            'solicitacao_id' => $this->solicitacao->id,
        ];
    }
}
