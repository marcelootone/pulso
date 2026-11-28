<?php

namespace App\Policies;

use App\Models\EventoAgenda;
use App\Models\User;

class EventoAgendaPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function gerenciarComuns(User $user): bool
    {
        $cargos = [User::TIPO_ADMINISTRADOR, User::TIPO_GESTOR, User::TIPO_SECRETARIA, 'Pedagogo', User::TIPO_COORDENADOR];

        return in_array($user->tipo_usuario, $cargos, true) || $user->hasAnyRole($cargos);
    }

    public function update(User $user, EventoAgenda $evento): bool
    {
        return $evento->compartilhado
            ? $this->gerenciarComuns($user)
            : (int) $evento->user_id === (int) $user->id;
    }

    public function delete(User $user, EventoAgenda $evento): bool
    {
        return $this->update($user, $evento);
    }
}
