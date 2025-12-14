<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreneauxController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BookingController;

// Routes API publiques (sans authentification)
Route::get('/disponibilite/{centreId}/{date}', [CreneauxController::class, 'getDisponibilite'])->name('api.disponibilite');
Route::get('/disponibilite-mois/{centreId}/{year}/{month}', [CreneauxController::class, 'getDisponibilitesMois'])->name('api.disponibilite.mois');
Route::post('/check-client', [ClientController::class, 'checkClient'])->name('api.check-client');
Route::post('/create-client', [ClientController::class, 'createClient'])->name('api.create-client');
Route::post('/create-rendez-vous', [BookingController::class, 'createRendezVous'])->name('api.create-rendez-vous');

// Routes QMS API publiques (pour les kiosks)
Route::get('/qms/centre/{centre}', [App\Http\Controllers\QmsController::class, 'getCentreInfo'])->name('api.qms.centre');
Route::get('/qms/services/{centre}', [App\Http\Controllers\QmsController::class, 'getServices'])->name('api.qms.services');
Route::get('/qms/queue/{centre}', [App\Http\Controllers\QmsController::class, 'getQueueData'])->name('api.qms.queue');
Route::post('/qms/check-rdv', [App\Http\Controllers\QmsController::class, 'checkRdv'])->name('api.qms.check-rdv');
Route::post('/qms/tickets', [App\Http\Controllers\QmsController::class, 'storeTicket'])->name('api.qms.tickets.store');
