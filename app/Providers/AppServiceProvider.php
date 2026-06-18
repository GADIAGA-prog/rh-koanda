<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UtilisateurPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Le modèle s'appelle User mais sa Policy suit le nommage métier français.
        Gate::policy(User::class, UtilisateurPolicy::class);
    }
}
