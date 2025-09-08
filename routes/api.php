<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreneauxController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BookingController;

// Routes API publiques (sans authentification)
Route::get('/disponibilite/{centreId}/{date}', [CreneauxController::class, 'getDisponibilite'])->name('api.disponibilite');
Route::post('/check-client', [ClientController::class, 'checkClient'])->name('api.check-client');
Route::post('/create-client', [ClientController::class, 'createClient'])->name('api.create-client');
Route::post('/create-rendez-vous', [BookingController::class, 'createRendezVous'])->name('api.create-rendez-vous');
