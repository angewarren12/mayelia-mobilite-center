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
use Illuminate\Support\Facades\Route;

// Page d'accueil institutionnelle
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Page d'accueil alternative (si nécessaire)
Route::get('/accueil', [App\Http\Controllers\HomeController::class, 'index'])->name('homepage');

// Route de test pour les services
Route::get('/test-services', function() {
    $services = App\Models\Service::with(['formules', 'documentsRequis'])
        ->where('statut', 'actif')
        ->orderBy('id', 'asc')
        ->take(4)
        ->get();
    
    return response()->json([
        'count' => $services->count(),
        'services' => $services->map(function($service) {
            return [
                'id' => $service->id,
                'nom' => $service->nom,
                'description' => $service->description,
                'formules_count' => $service->formules->count(),
                'documents_count' => $service->documentsRequis->count(),
                'statut' => $service->statut
            ];
        })
    ]);
});

// Routes publiques pour le wizard de rendez-vous
Route::prefix('booking')->name('booking.')->group(function () {
    // Wizard unifié
    Route::get('/wizard', function () {
        return view('booking.wizard');
    })->name('wizard');
    
    // Étape 1: Sélection du pays
    Route::get('/', [App\Http\Controllers\BookingController::class, 'index'])->name('index');
    
    // Étape 2: Sélection de la ville (AJAX)
    Route::get('/villes/{paysId}', [App\Http\Controllers\BookingController::class, 'getVilles'])->name('villes');
    
    // Étape 3: Sélection du centre (AJAX)
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

// Route publique pour le téléchargement du reçu
Route::get('/receipt/{rendezVousId}/download', [App\Http\Controllers\BookingController::class, 'downloadReceipt'])->name('receipt.download');

// Routes de suivi client (publiques)
Route::get('/clientconnect', [App\Http\Controllers\ClientTrackingController::class, 'showLogin'])->name('client.tracking.login');
Route::post('/clientconnect', [App\Http\Controllers\ClientTrackingController::class, 'login'])->name('client.tracking.login');
Route::post('/clientconnect/search', [App\Http\Controllers\ClientTrackingController::class, 'searchByTracking'])->name('client.tracking.search');
Route::get('/clientconnect/dashboard/{clientId}', [App\Http\Controllers\ClientTrackingController::class, 'dashboard'])->name('client.dashboard');

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
    Route::post('/creneaux/jours-travail/{jourTravail}/toggle', [CreneauxController::class, 'toggleJour'])->name('creneaux.jours-travail.toggle');
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

// Routes pour les étapes du workflow
Route::post('/dossier/{dossierOuvert}/etape1-fiche', [DossierWorkflowController::class, 'validerEtape1'])->name('dossier.etape1');
Route::post('/dossier/{dossierOuvert}/etape2-documents', [DossierWorkflowController::class, 'validerEtape2'])->name('dossier.etape2');
Route::post('/dossier/{dossierOuvert}/etape3-infos', [DossierWorkflowController::class, 'validerEtape3'])->name('dossier.etape3');
Route::post('/dossier/{dossierOuvert}/etape4-paiement', [DossierWorkflowController::class, 'validerEtape4'])->name('dossier.etape4');

// Routes pour les documents requis
Route::resource('document-requis', DocumentRequisController::class);
Route::get('/api/services/{service}/documents-requis', [DocumentRequisController::class, 'getByService'])->name('api.services.documents-requis');
Route::get('/api/services/{service}/documents-requis-by-type', [DocumentRequisController::class, 'getByType'])->name('api.services.documents-requis-by-type');

// Routes pour l'export
Route::post('/export/rendez-vous', [App\Http\Controllers\ExportController::class, 'exportRendezVous'])->name('export.rendez-vous');
    
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
    Route::resource('dossiers', DossierController::class);
    Route::post('/dossiers/{dossier}/update-documents', [DossierController::class, 'updateDocuments'])->name('dossiers.update-documents');
    Route::post('/dossiers/{dossier}/update-payment', [DossierController::class, 'updatePayment'])->name('dossiers.update-payment');

    // Routes pour les agents
    Route::resource('agents', AgentController::class);
    Route::post('/agents/{agent}/toggle-status', [AgentController::class, 'toggleStatus'])->name('agents.toggle-status');

    // Routes pour les dossiers
    Route::resource('dossiers', DossierController::class);
    Route::post('/dossiers/{dossier}/update-documents', [DossierController::class, 'updateDocuments'])->name('dossiers.update-documents');
    Route::post('/dossiers/{dossier}/update-payment', [DossierController::class, 'updatePayment'])->name('dossiers.update-payment');
    Route::post('/dossiers/{dossier}/update-biometrie', [DossierController::class, 'updateBiometrie'])->name('dossiers.update-biometrie');
    Route::post('/dossiers/{dossier}/validate', [DossierController::class, 'validate'])->name('dossiers.validate');
    Route::post('/dossiers/{dossier}/reschedule', [DossierController::class, 'reschedule'])->name('dossiers.reschedule');
    Route::post('/dossiers/open/{rendezVous}', [DossierController::class, 'open'])->name('dossiers.open');

    // Routes pour les documents requis
    Route::resource('document-requis', DocumentRequisController::class);

    // Services et Formules (lecture seule pour les admins de centre)
    Route::get('services', [ServiceController::class, 'index'])->name('services.index');
    
    // Jours de travail
    Route::resource('jours-travail', JourTravailController::class);
    Route::post('jours-travail/{jourTravail}/toggle', [JourTravailController::class, 'toggle'])->name('jours-travail.toggle');
    
    // Templates de créneaux - gérés par CreneauxController
    // Route::resource('templates', TemplateCreneauController::class); // Supprimé pour éviter les conflits
    // Route::post('templates/generate-creneaux', [TemplateCreneauController::class, 'generateCreneaux'])->name('templates.generate-creneaux');
    // Route::get('templates/get-formules', [TemplateCreneauController::class, 'getFormules'])->name('templates.get-formules');
    // Route::get('templates/get-tranches', [TemplateCreneauController::class, 'getTranchesHoraires'])->name('templates.get-tranches');
    
    // Exceptions - gérées par CreneauxController
    // Route::resource('exceptions', ExceptionController::class); // Supprimé pour éviter les conflits
    
    // Rendez-vous
    Route::resource('rendez-vous', RendezVousController::class);
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
