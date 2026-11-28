<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability, $arguments) {
            // O cadastro de alunos deve respeitar a restrição docente, inclusive para a gestão.
            if ($ability === 'cadastrar alunos') {
                return null;
            }

            // A edição de reservas deve respeitar o horário de início, inclusive para a gestão.
            if ($ability === 'update' && ($arguments[0] ?? null) instanceof \App\Models\Agendamento) {
                return null;
            }

            return $user->hasRole([\App\Models\User::TIPO_GESTOR, \App\Models\User::TIPO_ADMINISTRADOR]) || in_array($user->tipo_usuario, [\App\Models\User::TIPO_GESTOR, \App\Models\User::TIPO_ADMINISTRADOR]) ? true : null;
        });

        Gate::define('cadastrar alunos', function (User $user): bool {
            $cargosDocentes = [User::TIPO_PROFESSOR, User::TIPO_PROF_ESTUDO_ORIENTADO, User::TIPO_PROF_ESPECIAL];

            return ! in_array($user->tipo_usuario, $cargosDocentes, true)
                && ! $user->hasAnyRole($cargosDocentes);
        });
    }
}
