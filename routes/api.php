<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreneauxController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BookingController;

// Middleware de rate limiting : 60 requêtes par minute par IP
// Pour les kiosks qui peuvent faire beaucoup de requêtes, augmenter si nécessaire
Route::middleware(['throttle:60,1'])->group(function () {
    
    // Routes API publiques (sans authentification)
    Route::get('/disponibilite/{centreId}/{date}', [CreneauxController::class, 'getDisponibilite'])->name('api.disponibilite');
    Route::get('/disponibilite-mois/{centreId}/{year}/{month}', [CreneauxController::class, 'getDisponibilitesMois'])->name('api.disponibilite.mois');
    Route::post('/check-client', [ClientController::class, 'checkClient'])->name('api.check-client');
    Route::post('/create-client', [ClientController::class, 'createClient'])->name('api.create-client');
    Route::post('/create-rendez-vous', [BookingController::class, 'createRendezVous'])->name('api.create-rendez-vous');

    // Routes QMS API publiques (pour les kiosks)
    // Rate limiting plus permissif pour les kiosks : 120 requêtes par minute
    Route::middleware(['throttle:120,1'])->prefix('qms')->group(function () {
        Route::get('/centre/{centre}', [App\Http\Controllers\QmsController::class, 'getCentreInfo'])->name('api.qms.centre');
        Route::get('/services/{centre}', [App\Http\Controllers\QmsController::class, 'getServices'])->name('api.qms.services');
        Route::get('/queue/{centre}', [App\Http\Controllers\QmsController::class, 'getQueueData'])->name('api.qms.queue');
        Route::post('/check-rdv', [App\Http\Controllers\QmsController::class, 'checkRdv'])->name('api.qms.check-rdv');
        Route::post('/tickets', [App\Http\Controllers\QmsController::class, 'storeTicket'])->name('api.qms.tickets.store');
    });
});
