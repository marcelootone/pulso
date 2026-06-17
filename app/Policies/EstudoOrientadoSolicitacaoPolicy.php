<?php

namespace App\Policies;

use App\Models\EstudoOrientadoSolicitacao;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EstudoOrientadoSolicitacaoPolicy
{
    /**
     * Professor Regular pode criar solicitação.
     */
    public function criarSolicitacao(User $user): bool
    {
        return $user->hasPermissionTo('criar solicitacao estudo orientado');
    }

    /**
     * O solicitante ou perfis de gestão podem ver a solicitação.
     */
    public function verSolicitacao(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        if ($user->hasPermissionTo('consultar estudo orientado') || $user->hasPermissionTo('analisar solicitacao estudo orientado')) {
            return true;
        }

        return $solicitacao->professor_solicitante_id === $user->id;
    }

    /**
     * Coordenador pode analisar (listar e abrir análise).
     */
    public function analisar(User $user): bool
    {
        return $user->hasPermissionTo('analisar solicitacao estudo orientado');
    }

    /**
     * Aprovar solicitação (Coordenador).
     */
    public function aprovar(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        return $user->hasPermissionTo('analisar solicitacao estudo orientado') && $solicitacao->status === 'Pendente';
    }

    /**
     * Rejeitar solicitação (Coordenador).
     */
    public function rejeitar(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        return $user->hasPermissionTo('analisar solicitacao estudo orientado') && $solicitacao->status === 'Pendente';
    }

    /**
     * Atribuir orientador (Coordenador).
     */
    public function atribuirOrientador(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        return $user->hasPermissionTo('analisar solicitacao estudo orientado') && in_array($solicitacao->status, ['Aprovada', 'EmAtendimento']);
    }

    /**
     * Acessar acompanhamento (Orientador).
     */
    public function acompanhar(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        if ($user->hasPermissionTo('analisar solicitacao estudo orientado') || $user->hasPermissionTo('consultar estudo orientado')) {
            return true;
        }

        return $user->hasPermissionTo('registrar atendimento estudo orientado') && $solicitacao->professor_orientador_id === $user->id;
    }

    /**
     * Registrar atendimento (Orientador).
     */
    public function registrarAtendimento(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        return $user->hasPermissionTo('registrar atendimento estudo orientado') && 
               $solicitacao->professor_orientador_id === $user->id && 
               $solicitacao->status === 'EmAtendimento';
    }

    /**
     * Registrar evolução (Orientador).
     */
    public function registrarEvolucao(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        return $this->registrarAtendimento($user, $solicitacao);
    }

    /**
     * Criar plano de ação (Orientador).
     */
    public function criarPlanoAcao(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        return $this->registrarAtendimento($user, $solicitacao);
    }

    /**
     * Concluir acompanhamento (Orientador ou Coordenador).
     */
    public function concluir(User $user, EstudoOrientadoSolicitacao $solicitacao): bool
    {
        if ($solicitacao->status !== 'EmAtendimento') {
            return false;
        }

        if ($user->hasPermissionTo('analisar solicitacao estudo orientado')) {
            return true;
        }

        return $user->hasPermissionTo('registrar atendimento estudo orientado') && $solicitacao->professor_orientador_id === $user->id;
    }

    /**
     * Consulta global (Secretaria, Gestor, etc).
     */
    public function consultar(User $user): bool
    {
        return $user->hasPermissionTo('consultar estudo orientado') || $user->hasPermissionTo('analisar solicitacao estudo orientado');
    }
}
