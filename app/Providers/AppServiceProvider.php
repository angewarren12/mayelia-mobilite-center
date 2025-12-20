<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use App\Services\AuthService;
use App\Events\RendezVousCreated;
use App\Events\TicketCreated;
use App\Events\DossierOpened;
use App\Listeners\SendRendezVousConfirmation;
use App\Listeners\RecalculateTicketPriorities;
use App\Listeners\UpdateRendezVousStatus;

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
        // Helpers pour les vues Blade
        Blade::if('userCan', function ($module, $action) {
            $authService = app(AuthService::class);
            return $authService->hasPermission($module, $action);
        });

        Blade::if('isAdmin', function () {
            $authService = app(AuthService::class);
            return $authService->isAdmin();
        });

        Blade::if('isAgent', function () {
            $authService = app(AuthService::class);
            return $authService->isAgent();
        });

        // Enregistrement des Events et Listeners
        Event::listen(
            RendezVousCreated::class,
            SendRendezVousConfirmation::class
        );

        Event::listen(
            TicketCreated::class,
            RecalculateTicketPriorities::class
        );

        Event::listen(
            DossierOpened::class,
            UpdateRendezVousStatus::class
        );

        // Observers
        \App\Models\DossierOuvert::observe(\App\Observers\DossierOuvertObserver::class);
    }
}
