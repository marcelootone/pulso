<?php

namespace App\Policies;

use App\Models\Agendamento;
use App\Models\User;
use Carbon\CarbonImmutable;

class AgendamentoPolicy
{
    public function update(User $user, Agendamento $agendamento): bool
    {
        $inicio = CarbonImmutable::parse($agendamento->data.' '.$agendamento->horario_inicio, config('app.timezone'));

        if (! $inicio->isFuture()) {
            return false;
        }

        $gestao = [User::TIPO_SECRETARIA, User::TIPO_PEDAGOGO, User::TIPO_GESTOR, User::TIPO_ADMINISTRADOR];

        return (int) $agendamento->user_id === (int) $user->id
            || in_array($user->tipo_usuario, $gestao, true)
            || $user->hasAnyRole($gestao);
    }
}
