<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Services\AuthService;

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
    }
}
