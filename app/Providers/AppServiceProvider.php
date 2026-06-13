<?php

namespace App\Providers;

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
        Gate::before(function ($user, $ability) {
            return $user->hasRole([\App\Models\User::TIPO_GESTOR, \App\Models\User::TIPO_ADMINISTRADOR]) || in_array($user->tipo_usuario, [\App\Models\User::TIPO_GESTOR, \App\Models\User::TIPO_ADMINISTRADOR]) ? true : null;
        });
    }
}
