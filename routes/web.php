<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CentreController;
use App\Http\Controllers\CreneauxController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\JourTravailController;
use App\Http\Controllers\TemplateCreneauController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\DossierWorkflowController;
use App\Http\Controllers\DocumentRequisController;
use App\Http\Controllers\OneciTransferController;
use App\Http\Controllers\OneciController;
use App\Http\Controllers\OneciRecuperationController;
use Illuminate\Support\Facades\Route;

// Redirection de la page d'accueil vers le wizard de réservation
Route::get('/', function () {
    return redirect()->route('booking.wizard');
});

// Page d'accueil institutionnelle (accessible via /accueil)
Route::get('/accueil', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Routes publiques pour le wizard de rendez-vous
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/verification', [App\Http\Controllers\BookingController::class, 'showVerification'])->name('verification');
    Route::post('/verify-enrollment', [App\Http\Controllers\BookingController::class, 'verifyPreEnrollment'])->name('verify-enrollment');
    Route::post('/verify-cni', [App\Http\Controllers\BookingController::class, 'verifyPreEnrollmentCni'])->name('verify-cni');
    Route::post('/verify-carte-resident', [App\Http\Controllers\BookingController::class, 'verifyCarteResident'])->name('verify-carte-resident');
    Route::get('/verify-token/{token}', [App\Http\Controllers\BookingController::class, 'verifyToken'])->name('verify-token');
    Route::post('/clear-oneci-session', [App\Http\Controllers\BookingController::class, 'clearOneciSession'])->name('clear-oneci-session');
    
    // Redirection vers le pré-enrôlement ONECI avec identifiant Mayelia
    Route::get('/oneci-redirect', [App\Http\Controllers\BookingController::class, 'redirectToOneciPreEnrollment'])->name('oneci-redirect');
    
    
    // Wizard unifié (nécessite vérification ONECI)
    // Wizard unifié (Accès direct, vérification intégrée)
    Route::get('/wizard', function () {
        // Récupérer les services pour l'étape 1
        $services = \App\Models\Service::where('statut', 'actif')->with('formules')->get();
        
        // Passer les données ONECI à la vue si elles existent
        $oneciData = session('oneci_data');
        
        return view('booking.wizard', compact('oneciData', 'services'));
    })->name('wizard');
    
    // Étape 1: Sélection du Service (Page d'accueil)
    Route::get('/', [App\Http\Controllers\BookingController::class, 'index'])->name('index');
    
    // Étape 3: Sélection de la localisation basée sur le service (AJAX)
    Route::get('/locations/{serviceId}', [App\Http\Controllers\BookingController::class, 'getLocationsForService'])->name('locations');
    Route::get('/centres/{villeId}/{serviceId}', [App\Http\Controllers\BookingController::class, 'getCentresForService'])->name('centres-service');
    
    // Anciennes routes (à garder pour compatibilité temporaire ou supprimer plus tard)
    Route::get('/villes/{paysId}', [App\Http\Controllers\BookingController::class, 'getVilles'])->name('villes');
    Route::get('/centres/{villeId}', [App\Http\Controllers\BookingController::class, 'getCentres'])->name('centres');
    
    // Étape 4: Sélection du service (AJAX)
    Route::get('/services/{centreId}', [App\Http\Controllers\BookingController::class, 'getServices'])->name('services');
    
    // Étape 5: Sélection de la formule (AJAX)
    Route::get('/formules/{centreId}/{serviceId}', [App\Http\Controllers\BookingController::class, 'getFormules'])->name('formules');
    
    // Étape 6: Calendrier de disponibilité
    Route::get('/calendrier/{centreId}/{serviceId}/{formuleId}', [App\Http\Controllers\BookingController::class, 'calendrier'])->name('calendrier');
    
    // Étape 7: Formulaire client
    Route::get('/client/{centreId}/{serviceId}/{formuleId}/{date}/{tranche}', [App\Http\Controllers\BookingController::class, 'clientForm'])->name('client');
    
    // Étape 8: Traitement du paiement
    Route::post('/paiement', [App\Http\Controllers\BookingController::class, 'processPayment'])->name('payment');
    
    // Étape 9: Confirmation et reçu
    Route::get('/confirmation/{rendezVousId}', [App\Http\Controllers\BookingController::class, 'confirmation'])->name('confirmation');
    
});

// Webhook ONECI (route publique pour recevoir les notifications)
Route::post('/api/oneci/webhook/status-update', [App\Http\Controllers\OneciWebhookController::class, 'receiveStatusUpdate'])
    ->name('oneci.webhook.status');

// Route publique pour le téléchargement du reçu
Route::get('/receipt/{rendezVousId}/download', [App\Http\Controllers\BookingController::class, 'downloadReceipt'])->name('receipt.download');

// Routes de suivi client (publiques)
Route::get('/clientconnect', [App\Http\Controllers\ClientTrackingController::class, 'showLogin'])->name('client.tracking.login');
Route::post('/clientconnect', [App\Http\Controllers\ClientTrackingController::class, 'login'])->name('client.tracking.login.submit');
Route::post('/clientconnect/search', [App\Http\Controllers\ClientTrackingController::class, 'searchByTracking'])->name('client.tracking.search');
Route::get('/clientconnect/dashboard/{clientId}', [App\Http\Controllers\ClientTrackingController::class, 'dashboard'])->name('client.dashboard');

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes QMS Publiques (Kiosk et Affichage - Accessibles sans authentification)
Route::prefix('qms')->name('qms.')->group(function () {
    // Interfaces publiques
    Route::get('/kiosk/{centre}', [App\Http\Controllers\QmsController::class, 'kiosk'])->name('kiosk');
    Route::get('/display/{centre}', [App\Http\Controllers\QmsController::class, 'display'])->name('display');
    
    // API publiques pour le kiosk
    Route::post('/check-rdv', [App\Http\Controllers\QmsController::class, 'checkRdv'])->name('check-rdv');
    Route::post('/tickets', [App\Http\Controllers\QmsController::class, 'storeTicket'])->name('tickets.store');
    Route::get('/tickets/{ticket}/print', [App\Http\Controllers\QmsController::class, 'printTicket'])->name('tickets.print');
    
    // API Data publiques
    Route::get('/api/queue/{centre}', [App\Http\Controllers\QmsController::class, 'getQueueData'])->name('api.queue');
    Route::get('/api/services/{centre}', [App\Http\Controllers\QmsController::class, 'getServices'])->name('api.services');
});

// Routes protégées
Route::middleware(['auth', 'oneci.redirect'])->group(function () {
    // Dashboard (Mayelia uniquement - les agents ONECI sont redirigés)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/rendez-vous', [DashboardController::class, 'getRendezVousByMonth'])->name('api.dashboard.rendez-vous');
    
    // Gestion du centre
    Route::get('/centres', [CentreController::class, 'index'])->name('centres.index');
    Route::post('/centres/services/{service}/toggle', [CentreController::class, 'toggleService'])->name('centres.toggle-service');
    Route::post('/centres/formules/{formule}/toggle', [CentreController::class, 'toggleFormule'])->name('centres.toggle-formule');
    
    // Gestion des créneaux
    Route::get('/creneaux', [CreneauxController::class, 'index'])->name('creneaux.index');
    Route::get('/creneaux/templates', [CreneauxController::class, 'templates'])->name('creneaux.templates');
    Route::get('/creneaux/exceptions', [CreneauxController::class, 'exceptions'])->name('creneaux.exceptions');
    Route::get('/creneaux/calendrier', [CreneauxController::class, 'calendrier'])->name('creneaux.calendrier');
    Route::post('/creneaux/jours-travail', [CreneauxController::class, 'store'])->name('creneaux.jours-travail.store');
    Route::post('/templates/generate-creneaux', [CreneauxController::class, 'generateCreneaux'])->name('templates.generate-creneaux');
Route::post('/templates/store', [CreneauxController::class, 'storeTemplate'])->name('templates.store');
Route::post('/templates/store-bulk', [CreneauxController::class, 'storeBulkTemplates'])->name('templates.store-bulk');
Route::get('/templates/for-tranche', [CreneauxController::class, 'getTemplatesForTranche'])->name('templates.for-tranche');
Route::delete('/templates/{template}', [CreneauxController::class, 'destroyTemplate'])->name('templates.destroy');
Route::post('/creneaux/jours-travail/{jourTravail}/intervalle', [CreneauxController::class, 'updateIntervalle'])->name('creneaux.jours-travail.intervalle');
Route::post('/creneaux/jours-travail/{jourTravail}/migrate-intervalle', [CreneauxController::class, 'migrateIntervalle'])->name('creneaux.jours-travail.migrate-intervalle');
Route::post('/creneaux/jours-travail/{jourTravail}/force-migration', [CreneauxController::class, 'forceMigration'])->name('creneaux.jours-travail.force-migration');
Route::post('/creneaux/check-conflicts', [CreneauxController::class, 'checkConflicts'])->name('creneaux.check-conflicts');

// Routes pour les exceptions (gérées par CreneauxController)
Route::get('/exceptions/{exception}', [CreneauxController::class, 'getException'])->name('exceptions.show');
Route::post('/exceptions', [CreneauxController::class, 'storeException'])->name('exceptions.store');
Route::put('/exceptions/{exception}', [CreneauxController::class, 'updateException'])->name('exceptions.update');
    Route::delete('/exceptions/{exception}', [CreneauxController::class, 'destroyException'])->name('exceptions.destroy');
    
    // Routes pour les clients
    Route::resource('clients', ClientController::class);
    Route::get('/api/clients/search', [ClientController::class, 'search'])->name('api.clients.search');
    Route::post('/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');

    // Routes pour les rendez-vous
    Route::get('/rendez-vous', [RendezVousController::class, 'index'])->name('rendez-vous.index');
    Route::get('/rendez-vous/create', [RendezVousController::class, 'create'])->name('rendez-vous.create');
    Route::post('/rendez-vous', [RendezVousController::class, 'store'])->name('rendez-vous.store');
    Route::get('/rendez-vous/{rendezVous}', [RendezVousController::class, 'show'])->name('rendez-vous.show');
    Route::get('/rendez-vous/{rendezVous}/edit', [RendezVousController::class, 'edit'])->name('rendez-vous.edit');
    Route::put('/rendez-vous/{rendezVous}', [RendezVousController::class, 'update'])->name('rendez-vous.update');
    Route::delete('/rendez-vous/{rendezVous}', [RendezVousController::class, 'destroy'])->name('rendez-vous.destroy');
    Route::get('/api/services/{service}/formules', [RendezVousController::class, 'getFormulesByService'])->name('api.services.formules');
Route::post('/rendez-vous/{rendezVous}/open-dossier', [DossierWorkflowController::class, 'openDossier'])->name('rendez-vous.open-dossier')->withoutMiddleware(['csrf']);
Route::get('/dossier/{dossierOuvert}/workflow', [DossierWorkflowController::class, 'showWorkflow'])->name('dossier.workflow');
Route::get('/dossier/{dossierOuvert}/imprimer-recu', [DossierWorkflowController::class, 'imprimerRecu'])->name('dossier.imprimer-recu');
Route::get('/dossier/{dossierOuvert}/imprimer-etiquette', [DossierWorkflowController::class, 'imprimerEtiquette'])->name('dossier.imprimer-etiquette');

// Routes pour les étapes du workflow
Route::post('/dossier/{dossierOuvert}/etape1-fiche', [DossierWorkflowController::class, 'validerEtape1'])->name('dossier.etape1');
Route::post('/dossier/{dossierOuvert}/etape2-documents', [DossierWorkflowController::class, 'validerEtape2'])->name('dossier.etape2');
Route::post('/dossier/{dossierOuvert}/etape3-infos', [DossierWorkflowController::class, 'validerEtape3'])->name('dossier.etape3');
Route::post('/dossier/{dossierOuvert}/etape4-paiement', [DossierWorkflowController::class, 'validerEtape4'])->name('dossier.etape4');
Route::post('/dossier/{dossierOuvert}/rejeter', [DossierWorkflowController::class, 'rejeter'])->name('dossier.rejeter');
Route::post('/dossier/{dossierOuvert}/reset', [DossierWorkflowController::class, 'resetDossier'])->name('dossier.reset');
Route::post('/dossier/{dossierOuvert}/finaliser', [DossierWorkflowController::class, 'finaliser'])->name('dossier.finaliser');
Route::get('/document-verification/{documentVerification}/view', [DossierWorkflowController::class, 'viewDocument'])->name('dossier.view-document');

// Routes pour les documents requis
Route::resource('document-requis', DocumentRequisController::class);
Route::get('/api/services/{service}/documents-requis', [DocumentRequisController::class, 'getByService'])->name('api.services.documents-requis');
Route::get('/api/services/{service}/documents-requis-by-type', [DocumentRequisController::class, 'getByType'])->name('api.services.documents-requis-by-type');
Route::get('/api/centres/{centre}/services', [CentreController::class, 'getServicesByCentre'])->name('api.centres.services');

// Routes pour l'export
Route::post('/export/rendez-vous', [App\Http\Controllers\ExportController::class, 'exportRendezVous'])->name('export.rendez-vous');
Route::post('/export/dossiers', [App\Http\Controllers\ExportController::class, 'exportDossiers'])->name('export.dossiers');
    
    // Route de test temporaire
    Route::get('/test-login', function() {
        $user = App\Models\User::where('email', 'test@test.com')->first();
        if ($user) {
            Auth::login($user);
            return redirect('/rendez-vous')->with('success', 'Connecté en tant que ' . $user->nom);
        }
        return 'Utilisateur de test non trouvé';
    });

    // Routes pour les dossiers
    Route::get('/dossiers/create-walkin', [DossierController::class, 'createWalkin'])->name('dossiers.create-walkin');
    Route::post('/dossiers/store-walkin', [DossierController::class, 'storeWalkin'])->name('dossiers.store-walkin');
    Route::post('/dossiers/open/{rendezVous}', [DossierController::class, 'open'])->name('dossiers.open');
    
    Route::resource('dossiers', DossierController::class);
    
    Route::get('/dossiers/{dossierOuvert}/imprimer-etiquette', [DossierController::class, 'imprimerEtiquette'])->name('dossiers.imprimer-etiquette');
    Route::post('/dossiers/{dossier}/update-documents', [DossierController::class, 'updateDocuments'])->name('dossiers.update-documents');
    Route::post('/dossiers/{dossier}/update-payment', [DossierController::class, 'updatePayment'])->name('dossiers.update-payment');
    Route::post('/dossiers/{dossier}/update-biometrie', [DossierController::class, 'updateBiometrie'])->name('dossiers.update-biometrie');
    Route::post('/dossiers/{dossier}/validate', [DossierController::class, 'validate'])->name('dossiers.validate');
    Route::post('/dossiers/{dossier}/reschedule', [DossierController::class, 'reschedule'])->name('dossiers.reschedule');

    // Routes pour les agents
    Route::resource('agents', AgentController::class);
    Route::post('/agents/{agent}/toggle-status', [AgentController::class, 'toggleStatus'])->name('agents.toggle-status');

    // Routes pour les documents requis
    Route::resource('document-requis', DocumentRequisController::class);

    // Services et Formules (lecture seule pour les admins de centre)
    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    
    // Jours de travail - Routes spécifiques AVANT le resource
    Route::post('jours-travail/{jourTravail}/toggle', [JourTravailController::class, 'toggle'])->name('jours-travail.toggle');
    Route::post('jours-travail/{jourTravail}/horaires', [JourTravailController::class, 'updateHoraires'])->name('jours-travail.horaires');
    Route::resource('jours-travail', JourTravailController::class);
    
    // Templates de créneaux - gérés par CreneauxController
    // Route::resource('templates', TemplateCreneauController::class); // Supprimé pour éviter les conflits
    // Route::post('templates/generate-creneaux', [TemplateCreneauController::class, 'generateCreneaux'])->name('templates.generate-creneaux');
    // Route::get('templates/get-formules', [TemplateCreneauController::class, 'getFormules'])->name('templates.get-formules');
    // Route::get('templates/get-tranches', [TemplateCreneauController::class, 'getTranchesHoraires'])->name('templates.get-tranches');
    
    // Exceptions - gérées par CreneauxController
    // Route::resource('exceptions', ExceptionController::class); // Supprimé pour éviter les conflits
    
    // Rendez-vous
    // Routes resource déjà définies manuellement ci-dessus (lignes 160-166)
    // Route::resource('rendez-vous', RendezVousController::class); // Commenté pour éviter duplication
    
    // Statistiques
    Route::get('/statistics', [App\Http\Controllers\StatisticsController::class, 'index'])->name('statistics.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::post('/notifications/{notification}/read', function(\App\Models\Notification $notification) {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.read');
    
    Route::post('/notifications/mark-all-read', function() {
        $user = auth()->user();
        \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    })->name('notifications.mark-all-read');

    // Routes ONECI (pour les agents ONECI uniquement - LECTURE SEULE)
    Route::prefix('oneci')->name('oneci.')->middleware('oneci.redirect')->group(function () {
        Route::get('/dashboard', [OneciController::class, 'dashboard'])->name('dashboard');
        Route::get('/dossiers', [OneciController::class, 'dossiers'])->name('dossiers');
        Route::get('/dossiers/{item}/workflow', [OneciController::class, 'voirWorkflow'])->name('dossiers.workflow');
        Route::get('/transferts/{transfer}', [OneciController::class, 'voirTransfert'])->name('transferts.detail');
        
        // Actions scanner (restaurées)
        Route::get('/scanner', [OneciController::class, 'scanner'])->name('scanner');
        Route::post('/scanner/code', [OneciController::class, 'scannerCode'])->name('scanner.code');
        Route::post('/dossiers/{item}/carte-prete', [OneciController::class, 'marquerCartePrete'])->name('dossiers.carte-prete');
        Route::get('/cartes-prete', [OneciController::class, 'dossiersCartesPrete'])->name('cartes-prete');
    });

    // Routes transferts ONECI (pour les agents Mayelia)
    Route::prefix('oneci-transfers')->name('oneci-transfers.')->group(function () {
        Route::get('/', [OneciTransferController::class, 'index'])->name('index');
        Route::get('/create', [OneciTransferController::class, 'create'])->name('create');
        Route::post('/', [OneciTransferController::class, 'store'])->name('store');
        Route::get('/{oneciTransfer}', [OneciTransferController::class, 'show'])->name('show');
        Route::post('/{oneciTransfer}/envoyer', [OneciTransferController::class, 'envoyer'])->name('envoyer');
        Route::get('/{oneciTransfer}/imprimer-etiquettes', [OneciTransferController::class, 'imprimerEtiquettes'])->name('imprimer-etiquettes');
    });

    // Routes récupération (pour les agents Mayelia)
    Route::prefix('oneci-recuperation')->name('oneci-recuperation.')->group(function () {
        Route::get('/cartes-prete', [OneciRecuperationController::class, 'cartesPrete'])->name('cartes-prete');
        Route::get('/scanner', [OneciRecuperationController::class, 'scannerRecuperation'])->name('scanner');
        Route::get('/scanner-lot', [OneciRecuperationController::class, 'scannerLot'])->name('scanner-lot');
        Route::post('/scanner/code', [OneciRecuperationController::class, 'scannerCodeRecuperation'])->name('scanner.code');
        Route::post('/{item}/confirmer', [OneciRecuperationController::class, 'confirmerRecuperation'])->name('confirmer');
        Route::post('/confirmer-lot', [OneciRecuperationController::class, 'confirmerRecuperationLot'])->name('confirmer-lot');
    });

    // Routes QMS Protégées (Agent et gestion des tickets - Nécessitent authentification)
    Route::prefix('qms')->name('qms.')->group(function () {
        // Interface Agent (protégée)
        Route::get('/agent', [App\Http\Controllers\QmsController::class, 'agent'])->name('agent');
        
        // Actions sur les tickets (protégées - réservées aux agents)
    Route::post('/tickets/call-next', [App\Http\Controllers\QmsController::class, 'callTicket'])->name('tickets.callNext');
    Route::post('/tickets/{ticket}/call', [App\Http\Controllers\QmsController::class, 'callTicket'])->name('tickets.call');
    Route::post('/tickets/{ticket}/complete', [App\Http\Controllers\QmsController::class, 'completeTicket'])->name('tickets.complete');
    Route::post('/tickets/{ticket}/cancel', [App\Http\Controllers\QmsController::class, 'cancelTicket'])->name('tickets.cancel');
    Route::post('/tickets/{ticket}/recall', [App\Http\Controllers\QmsController::class, 'recallTicket'])->name('tickets.recall');
    });

    // Routes Admin - Paramètres QMS des Centres
    Route::prefix('admin/centres')->name('admin.centres.')->group(function () {
        Route::get('/{centre}/qms-settings', [App\Http\Controllers\CentreQmsSettingsController::class, 'edit'])->name('qms.edit');
        Route::put('/{centre}/qms-settings', [App\Http\Controllers\CentreQmsSettingsController::class, 'update'])->name('qms.update');
    });

    // Routes Admin - Gestion des Guichets
    Route::prefix('admin/guichets')->name('admin.guichets.')->group(function () {
        Route::get('/', [App\Http\Controllers\GuichetManagementController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\GuichetManagementController::class, 'store'])->name('store');
        Route::put('/{guichet}', [App\Http\Controllers\GuichetManagementController::class, 'update'])->name('update');
        Route::delete('/{guichet}', [App\Http\Controllers\GuichetManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{guichet}/toggle-status', [App\Http\Controllers\GuichetManagementController::class, 'toggleStatus'])->name('toggle-status');
    });
});

require __DIR__.'/auth.php';
