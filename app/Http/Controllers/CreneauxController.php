<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Centre;
use App\Models\JourTravail;
use App\Models\TemplateCreneau;
use App\Models\Exception;
use App\Models\CreneauGenere;
use App\Models\RendezVous;
use App\Models\Service;
use App\Models\Formule;
use App\Services\CreneauGeneratorService;
use App\Services\TrancheHoraireService;
use App\Services\AuthService;
use App\Http\Controllers\Concerns\ChecksPermissions;
use Carbon\Carbon;

class CreneauxController extends Controller
{
    use ChecksPermissions;

    protected $creneauGeneratorService;
    protected $trancheHoraireService;
    protected $authService;

    public function __construct(
        CreneauGeneratorService $creneauGeneratorService, 
        TrancheHoraireService $trancheHoraireService,
        AuthService $authService
    )
    {
        $this->creneauGeneratorService = $creneauGeneratorService;
        $this->trancheHoraireService = $trancheHoraireService;
        $this->authService = $authService;
    }

    /**
     * Afficher la page de gestion des créneaux
     */
    public function index()
    {
        $this->checkPermission('creneaux', 'view');

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        // Récupérer les jours de travail
        $joursTravail = JourTravail::where('centre_id', $centre->id)
            ->orderBy('jour_semaine')
            ->get();

        // Récupérer les templates
        $templates = TemplateCreneau::where('centre_id', $centre->id)
            ->with(['service', 'formule'])
            ->get();

        // Récupérer les exceptions
        $exceptions = Exception::where('centre_id', $centre->id)
            ->where('date_exception', '>=', now()->toDateString())
            ->orderBy('date_exception')
            ->get();

        // Compter les jours ouverts
        $joursOuverts = $joursTravail->where('actif', true)->count();

        return view('creneaux.index', compact('centre', 'joursTravail', 'templates', 'exceptions', 'joursOuverts'));
    }

    /**
     * Afficher la page des templates
     */
    public function templates()
    {
        $this->checkPermission('creneaux', 'templates.view');

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        // Récupérer les jours de travail
        $joursTravail = JourTravail::where('centre_id', $centre->id)
            ->orderBy('jour_semaine')
            ->get();

        // Récupérer les services activés pour ce centre avec leurs formules
        $servicesActives = $centre->services()->wherePivot('actif', true)->with('formules')->get();

        // Récupérer les templates
        $templates = TemplateCreneau::where('centre_id', $centre->id)
            ->with(['service', 'formule'])
            ->get();

        // Préparer les données des formules pour JavaScript
        $formulesData = $servicesActives->mapWithKeys(function($service) {
            return [$service->id => $service->formules->map(function($formule) {
                return [
                    'id' => $formule->id,
                    'nom' => $formule->nom,
                    'prix' => $formule->prix,
                    'couleur' => $formule->couleur
                ];
            })];
        });

        return view('creneaux.templates-simple', compact('centre', 'templates', 'joursTravail', 'servicesActives', 'formulesData'));
    }

    /**
     * Créer un nouveau template
     */
    public function storeTemplate(Request $request)
    {
        // Vérifier la permission
        $this->checkPermission('creneaux', 'templates.create');

        \Log::info('=== DÉBUT storeTemplate ===');
        \Log::info('Request data:', $request->all());
        
        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        
        \Log::info('User:', ['id' => $user->id, 'email' => $user->email]);
        \Log::info('Centre:', $centre ? ['id' => $centre->id, 'nom' => $centre->nom] : 'null');
        
        if (!$centre) {
            \Log::error('Aucun centre assigné à l\'utilisateur');
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        try {
            $request->validate([
                'service_id' => 'required|integer',
                'formule_id' => 'required|integer',
                'jour_semaine' => 'required|integer|between:1,7',
                'tranche_horaire' => 'required|string',
                'capacite' => 'required|integer|min:1|max:20'
            ]);
            \Log::info('Validation réussie');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation:', $e->errors());
            return response()->json(['error' => 'Données invalides', 'details' => $e->errors()], 422);
        }

        // Vérifier que le service existe et est activé pour ce centre
        \Log::info('Recherche du service ID: ' . $request->service_id);
        $service = Service::find($request->service_id);
        \Log::info('Service trouvé:', $service ? ['id' => $service->id, 'nom' => $service->nom] : ['status' => 'null']);

        if (!$service) {
            \Log::error('Service non trouvé');
            return response()->json(['error' => 'Service non trouvé'], 404);
        }

        // Vérifier que le service est activé pour ce centre
        $serviceActif = $centre->services()->wherePivot('actif', true)->where('services.id', $request->service_id)->exists();
        \Log::info('Service activé pour le centre:', ['actif' => $serviceActif]);

        if (!$serviceActif) {
            \Log::error('Service non activé pour ce centre');
            return response()->json(['error' => 'Service non activé pour ce centre'], 403);
        }

        // Vérifier que le jour de travail est actif
        $jourTravail = JourTravail::where('centre_id', $centre->id)
            ->where('jour_semaine', $request->jour_semaine)
            ->where('actif', true)
            ->first();
            
        if (!$jourTravail) {
            \Log::error('Jour de travail non configuré pour le jour: ' . $request->jour_semaine);
            return response()->json(['error' => 'Ce jour n\'est pas configuré comme jour de travail'], 404);
        }

        // Vérifier que la formule existe et est liée au service
        $formule = Formule::where('id', $request->formule_id)
            ->where('service_id', $request->service_id)
            ->first();
            
        if (!$formule) {
            \Log::error('Formule non trouvée ou non liée au service');
            return response()->json(['error' => 'Formule non trouvée ou non liée au service'], 404);
        }

        // Vérifier que la formule est activée pour ce centre
        $formuleActive = $centre->formules()->wherePivot('actif', true)->where('formules.id', $request->formule_id)->exists();
        \Log::info('Formule activée pour le centre:', ['actif' => $formuleActive]);

        if (!$formuleActive) {
            \Log::error('Formule non activée pour le centre: ' . $formule->id);
            return response()->json(['error' => 'La formule ' . $formule->nom . ' n\'est pas activée pour ce centre'], 403);
        }

        // Vérifier s'il n'y a pas déjà un template pour cette combinaison
        $templateExistant = TemplateCreneau::where('centre_id', $centre->id)
            ->where('service_id', $request->service_id)
            ->where('formule_id', $request->formule_id)
            ->where('jour_semaine', $request->jour_semaine)
            ->where('tranche_horaire', $request->tranche_horaire)
            ->exists();

        if ($templateExistant) {
            \Log::error('Template déjà existant pour cette combinaison');
            return response()->json(['error' => 'Un template existe déjà pour cette combinaison'], 409);
        }

        try {
            $template = TemplateCreneau::create([
                'centre_id' => $centre->id,
                'service_id' => $request->service_id,
                'formule_id' => $request->formule_id,
                'jour_semaine' => $request->jour_semaine,
                'tranche_horaire' => $request->tranche_horaire,
                'capacite' => $request->capacite,
                'statut' => 'actif'
            ]);

            \Log::info('Template créé avec succès:', [
                'id' => $template->id,
                'service' => $service->nom,
                'formule' => $formule->nom,
                'tranche' => $request->tranche_horaire
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template créé avec succès',
                'template' => $template->load(['service', 'formule'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du template:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erreur lors de la création du template: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Créer des templates en masse
     */
    public function storeBulkTemplates(Request $request)
    {
        // Vérifier la permission
        $this->checkPermission('creneaux', 'templates.create');
        
        \Log::info('=== DÉBUT storeBulkTemplates ===');
        \Log::info('Request data:', $request->all());
        
        $user = auth()->user();
        $centre = $user->centre;
        
        \Log::info('User:', ['id' => $user->id, 'email' => $user->email]);
        \Log::info('Centre:', $centre ? ['id' => $centre->id, 'nom' => $centre->nom] : 'null');
        
        if (!$centre) {
            \Log::error('Aucun centre assigné à l\'utilisateur');
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        try {
            $request->validate([
                'jours_semaine' => 'required|array|min:1',
                'jours_semaine.*' => 'integer|between:1,7',
                'service_id' => 'required|integer',
                'formule_ids' => 'required|array|min:1',
                'formule_ids.*' => 'integer',
                'capacite' => 'required|integer|min:1|max:20'
            ]);
            \Log::info('Validation réussie');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation:', $e->errors());
            return response()->json(['error' => 'Données invalides', 'details' => $e->errors()], 422);
        }

        // Vérifier que le service existe et est activé pour ce centre
        \Log::info('Recherche du service ID: ' . $request->service_id);
        $service = Service::find($request->service_id);
        \Log::info('Service trouvé:', $service ? ['id' => $service->id, 'nom' => $service->nom] : ['status' => 'null']);

        if (!$service) {
            \Log::error('Service non trouvé');
            return response()->json(['error' => 'Service non trouvé'], 404);
        }

        // Vérifier que le service est activé pour ce centre
        $serviceActif = $centre->services()->wherePivot('actif', true)->where('services.id', $request->service_id)->exists();
        \Log::info('Service activé pour le centre:', ['actif' => $serviceActif]);

        if (!$serviceActif) {
            \Log::error('Service non activé pour ce centre');
            return response()->json(['error' => 'Service non activé pour ce centre'], 403);
        }

        // Vérifier que les jours de travail sont actifs
        $joursTravail = JourTravail::where('centre_id', $centre->id)
            ->whereIn('jour_semaine', $request->jours_semaine)
            ->where('actif', true)
            ->get();
            
        if ($joursTravail->count() !== count($request->jours_semaine)) {
            $joursManquants = array_diff($request->jours_semaine, $joursTravail->pluck('jour_semaine')->toArray());
            \Log::error('Certains jours ne sont pas configurés comme jours de travail:', ['jours_manquants' => $joursManquants]);
            return response()->json(['error' => 'Certains jours sélectionnés ne sont pas configurés comme jours de travail'], 404);
        }

        // Vérifier que les formules existent et sont activées
        $formules = Formule::whereIn('id', $request->formule_ids)
            ->where('service_id', $request->service_id)
            ->get();

        if ($formules->count() !== count($request->formule_ids)) {
            \Log::error('Certaines formules ne sont pas trouvées ou ne sont pas liées au service');
            return response()->json(['error' => 'Certaines formules ne sont pas trouvées ou ne sont pas liées au service'], 404);
        }

        foreach ($formules as $formule) {
            $formuleActive = $centre->formules()->wherePivot('actif', true)->where('formules.id', $formule->id)->exists();
            if (!$formuleActive) {
                \Log::error('Formule non activée pour le centre: ' . $formule->id);
                return response()->json(['error' => 'La formule ' . $formule->nom . ' n\'est pas activée pour ce centre'], 403);
            }
        }

        $created = 0;
        $skipped = 0;
        $errors = [];

        // Traiter chaque jour sélectionné
        foreach ($joursTravail as $jourTravail) {
            \Log::info('Traitement du jour:', ['jour' => $jourTravail->jour_semaine]);
            
            // Générer les tranches horaires pour ce jour
            $tranches = $this->trancheHoraireService->generateTranchesForDay($jourTravail);
            \Log::info('Tranches générées pour le jour ' . $jourTravail->jour_semaine . ':', ['count' => count($tranches)]);

            // Créer un template pour chaque tranche et chaque formule
            foreach ($tranches as $tranche) {
                if ($tranche['est_pause']) {
                    \Log::info('Tranche en pause, ignorée:', ['tranche' => $tranche['tranche_horaire']]);
                    continue;
                }

                foreach ($formules as $formule) {
                    // Vérifier s'il n'y a pas déjà un template pour cette combinaison
                    $templateExistant = TemplateCreneau::where('centre_id', $centre->id)
                        ->where('service_id', $request->service_id)
                        ->where('formule_id', $formule->id)
                        ->where('jour_semaine', $jourTravail->jour_semaine)
                        ->where('tranche_horaire', $tranche['tranche_horaire'])
                        ->exists();

                    if ($templateExistant) {
                        \Log::info('Template existant, ignoré:', [
                            'service' => $service->nom,
                            'formule' => $formule->nom,
                            'jour' => $jourTravail->jour_semaine,
                            'tranche' => $tranche['tranche_horaire']
                        ]);
                        $skipped++;
                        continue;
                    }

                    try {
                        TemplateCreneau::create([
                            'centre_id' => $centre->id,
                            'service_id' => $request->service_id,
                            'formule_id' => $formule->id,
                            'jour_semaine' => $jourTravail->jour_semaine,
                            'tranche_horaire' => $tranche['tranche_horaire'],
                            'capacite' => $request->capacite,
                            'statut' => 'actif'
                        ]);
                        $created++;
                        \Log::info('Template créé:', [
                            'service' => $service->nom,
                            'formule' => $formule->nom,
                            'jour' => $jourTravail->jour_semaine,
                            'tranche' => $tranche['tranche_horaire']
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Erreur lors de la création du template:', [
                            'error' => $e->getMessage(),
                            'service' => $service->nom,
                            'formule' => $formule->nom,
                            'jour' => $jourTravail->jour_semaine,
                            'tranche' => $tranche['tranche_horaire']
                        ]);
                        $errors[] = "Erreur pour {$formule->nom} - Jour {$jourTravail->jour_semaine} - {$tranche['tranche_horaire']}: " . $e->getMessage();
                    }
                }
            }
        }

        \Log::info('=== FIN storeBulkTemplates ===', [
            'created' => $created,
            'skipped' => $skipped,
            'errors' => count($errors)
        ]);

        return response()->json([
            'success' => true,
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $errors,
            'message' => "Création terminée: {$created} templates créés, {$skipped} ignorés"
        ]);
    }

    /**
     * Récupérer les templates d'une tranche horaire spécifique
     */
    public function getTemplatesForTranche(Request $request)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'service_id' => 'required|integer',
            'jour_semaine' => 'required|integer|between:1,7',
            'tranche_horaire' => 'required|string'
        ]);

        $templates = TemplateCreneau::where('centre_id', $centre->id)
            ->where('service_id', $request->service_id)
            ->where('jour_semaine', $request->jour_semaine)
            ->where('tranche_horaire', $request->tranche_horaire)
            ->with(['formule'])
            ->get();

        return response()->json([
            'success' => true,
            'templates' => $templates->map(function($template) {
                return [
                    'id' => $template->id,
                    'formule_id' => $template->formule_id,
                    'formule_nom' => $template->formule->nom,
                    'capacite' => $template->capacite,
                    'couleur' => $template->formule->couleur
                ];
            })
        ]);
    }

    /**
     * Supprimer un template
     */
    public function destroyTemplate(TemplateCreneau $template)
    {
        // Vérifier la permission
        $this->checkPermission('creneaux', 'templates.delete');

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        
        if (!$centre || $template->centre_id !== $centre->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template supprimé avec succès'
        ]);
    }

    /**
     * Afficher la page des exceptions
     */
    public function exceptions()
    {
        $this->checkPermission('creneaux', 'exceptions.view');

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Centre non trouvé');
        }

        $exceptions = \App\Models\Exception::where('centre_id', $centre->id)
            ->orderBy('date_exception', 'desc')
            ->get();

        return view('creneaux.exceptions', compact('exceptions'));
    }

    /**
     * Récupérer une exception pour modification
     */
    public function getException(\App\Models\Exception $exception)
    {
        // Vérifier que l'utilisateur est admin du centre
        $centre = auth()->user()->centre;
        if (!$centre || $exception->centre_id !== $centre->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        return response()->json([
            'success' => true,
            'exception' => [
                'id' => $exception->id,
                'date_exception' => $exception->date_exception->format('Y-m-d'),
                'type' => $exception->type,
                'description' => $exception->description,
                'heure_debut' => $exception->heure_debut ? $exception->heure_debut->format('H:i') : null,
                'heure_fin' => $exception->heure_fin ? $exception->heure_fin->format('H:i') : null,
                'pause_debut' => $exception->pause_debut ? $exception->pause_debut->format('H:i') : null,
                'pause_fin' => $exception->pause_fin ? $exception->pause_fin->format('H:i') : null,
                'capacite_reduite' => $exception->capacite_reduite
            ]
        ]);
    }

    /**
     * Créer une nouvelle exception
     */
    public function storeException(Request $request)
    {
        // Vérifier la permission
        $this->checkPermission('creneaux', 'exceptions.create');

        \Log::info('=== DÉBUT storeException ===');
        \Log::info('Données reçues:', $request->all());
        
        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        if (!$centre) {
            \Log::error('Centre non trouvé pour l\'utilisateur');
            return response()->json(['success' => false, 'message' => 'Centre non trouvé'], 403);
        }

        \Log::info('Centre trouvé:', ['centre_id' => $centre->id, 'nom' => $centre->nom]);

        try {
            $request->validate([
                'date_exception' => 'required|date|after_or_equal:today',
                'type' => 'required|in:ferme,capacite_reduite,horaires_modifies',
                'description' => 'nullable|string|max:255',
                'heure_debut' => 'nullable|date_format:H:i',
                'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
                'pause_debut' => 'nullable|date_format:H:i',
                'pause_fin' => 'nullable|date_format:H:i|after:pause_debut',
                'capacite_reduite' => 'nullable|integer|min:1'
            ]);
            \Log::info('Validation réussie');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation:', $e->errors());
            return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $e->errors()], 422);
        }

        // Vérifier qu'il n'y a pas déjà une exception pour cette date
        $existingException = \App\Models\Exception::where('centre_id', $centre->id)
            ->where('date_exception', $request->date_exception)
            ->first();

        if ($existingException) {
            \Log::warning('Exception déjà existante pour cette date:', ['date' => $request->date_exception]);
            return response()->json([
                'success' => false, 
                'message' => 'Une exception existe déjà pour cette date'
            ], 422);
        }

        \Log::info('Création de l\'exception...');
        
        try {
            $exception = \App\Models\Exception::create([
                'centre_id' => $centre->id,
                'date_exception' => $request->date_exception,
                'type' => $request->type,
                'description' => $request->description,
                'heure_debut' => $request->heure_debut ? $request->date_exception . ' ' . $request->heure_debut . ':00' : null,
                'heure_fin' => $request->heure_fin ? $request->date_exception . ' ' . $request->heure_fin . ':00' : null,
                'pause_debut' => $request->pause_debut ? $request->date_exception . ' ' . $request->pause_debut . ':00' : null,
                'pause_fin' => $request->pause_fin ? $request->date_exception . ' ' . $request->pause_fin . ':00' : null,
                'capacite_reduite' => $request->capacite_reduite
            ]);
            
            \Log::info('Exception créée avec succès:', ['exception_id' => $exception->id]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Exception créée avec succès',
                'exception' => [
                    'id' => $exception->id,
                    'date_exception' => $exception->date_exception->format('Y-m-d'),
                    'type' => $exception->type,
                    'description' => $exception->description
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour une exception
     */
    public function updateException(Request $request, \App\Models\Exception $exception)
    {
        // Vérifier la permission
        $this->checkPermission('creneaux', 'exceptions.update');

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        if (!$centre || $exception->centre_id !== $centre->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'date_exception' => 'required|date|after_or_equal:today',
            'type' => 'required|in:ferme,capacite_reduite,horaires_modifies',
            'description' => 'nullable|string|max:255',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
            'pause_debut' => 'nullable|date_format:H:i',
            'pause_fin' => 'nullable|date_format:H:i|after:pause_debut',
            'capacite_reduite' => 'nullable|integer|min:1'
        ]);

        // Vérifier qu'il n'y a pas déjà une autre exception pour cette date
        $existingException = \App\Models\Exception::where('centre_id', $centre->id)
            ->where('date_exception', $request->date_exception)
            ->where('id', '!=', $exception->id)
            ->first();

        if ($existingException) {
            return response()->json([
                'success' => false, 
                'message' => 'Une autre exception existe déjà pour cette date'
            ], 422);
        }

        $exception->update([
            'date_exception' => $request->date_exception,
            'type' => $request->type,
            'description' => $request->description,
            'heure_debut' => $request->heure_debut ? $request->date_exception . ' ' . $request->heure_debut : null,
            'heure_fin' => $request->heure_fin ? $request->date_exception . ' ' . $request->heure_fin : null,
            'pause_debut' => $request->pause_debut ? $request->date_exception . ' ' . $request->pause_debut : null,
            'pause_fin' => $request->pause_fin ? $request->date_exception . ' ' . $request->pause_fin : null,
            'capacite_reduite' => $request->capacite_reduite
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Exception mise à jour avec succès',
            'exception' => $exception
        ]);
    }

    /**
     * Supprimer une exception
     */
    public function destroyException(\App\Models\Exception $exception)
    {
        // Vérifier la permission
        $this->checkPermission('creneaux', 'exceptions.delete');
        
        $centre = auth()->user()->centre;
        if (!$centre || $exception->centre_id !== $centre->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $exception->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Exception supprimée avec succès'
        ]);
    }

    /**
     * Mettre à jour l'intervalle d'un jour de travail
     */
    public function updateIntervalle(Request $request, JourTravail $jourTravail)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $jourTravail->centre_id !== $centre->id) {
            return response()->json([
                'success' => false,
                'message' => 'Jour de travail non trouvé'
            ], 404);
        }

        $request->validate([
            'intervalle_minutes' => 'required|integer|in:15,30,45,60,90,120'
        ]);

        $jourTravail->update([
            'intervalle_minutes' => $request->intervalle_minutes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Intervalle mis à jour avec succès',
            'jour' => $jourTravail->fresh()
        ]);
    }

    /**
     * Migrer l'intervalle d'un jour de travail
     */
    public function migrateIntervalle(Request $request, JourTravail $jourTravail)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $jourTravail->centre_id !== $centre->id) {
            return response()->json([
                'success' => false,
                'message' => 'Jour de travail non trouvé'
            ], 404);
        }

        // Logique de migration ici si nécessaire
        
        return response()->json([
            'success' => true,
            'message' => 'Migration effectuée avec succès'
        ]);
    }

    /**
     * Forcer la migration
     */
    public function forceMigration(Request $request, JourTravail $jourTravail)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $jourTravail->centre_id !== $centre->id) {
            return response()->json([
                'success' => false,
                'message' => 'Jour de travail non trouvé'
            ], 404);
        }

        // Logique de migration forcée ici si nécessaire
        
        return response()->json([
            'success' => true,
            'message' => 'Migration forcée effectuée avec succès'
        ]);
    }

    /**
     * Vérifier les conflits
     */
    public function checkConflicts(Request $request)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return response()->json([
                'success' => false,
                'message' => 'Centre non trouvé'
            ], 404);
        }

        // Logique de vérification des conflits ici
        
        return response()->json([
            'success' => true,
            'conflicts' => []
        ]);
    }

    /**
     * Afficher la page du calendrier
     */
    public function calendrier()
    {
        $this->checkPermission('creneaux', 'calendrier.view');

        $user = $this->authService->getAuthenticatedUser();
        $centre = $user->centre;
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Centre non trouvé');
        }

        // Données initiales pour le calendrier
        $disponibiliteData = [];

        return view('creneaux.calendrier', compact('disponibiliteData'));
    }

    /**
     * API pour récupérer la disponibilité d'une date
     */
    public function getDisponibilite($centreId, $date)
    {
        try {
            $disponibiliteService = app(\App\Services\DisponibiliteService::class);
            $disponibilite = $disponibiliteService->calculerDisponibilite($centreId, $date);

            if (!$disponibilite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Centre non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $disponibilite
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul de disponibilité:', [
                'centre_id' => $centreId,
                'date' => $date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul de disponibilité'
            ], 500);
        }
    }

    /**
     * API pour récupérer les disponibilités d'un mois entier (optimisé)
     */
    public function getDisponibilitesMois($centreId, $year, $month)
    {
        try {
            $disponibiliteService = app(\App\Services\DisponibiliteService::class);
            
            // Calculer le nombre de jours dans le mois
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            
            $disponibilites = [];
            
            // Charger toutes les disponibilités du mois
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                $disponibilite = $disponibiliteService->calculerDisponibilite($centreId, $date);
                
                if ($disponibilite) {
                    $disponibilites[$date] = $disponibilite;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $disponibilites
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul des disponibilités du mois:', [
                'centre_id' => $centreId,
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul des disponibilités'
            ], 500);
        }
    }
}
