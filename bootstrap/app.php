<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'oneci.redirect' => \App\Http\Middleware\RedirectOneciToDashboard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handler d'exceptions personnalisé est géré dans app/Exceptions/Handler.php
    })->create();
