<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ville;
use App\Models\Centre;
use App\Models\Service;
use App\Models\Formule;
use App\Models\Client;
use App\Models\RendezVous;
use App\Services\DisponibiliteService;
use App\Services\OneciVerificationService;
use App\Http\Requests\Booking\VerifyPreEnrollmentRequest;
use App\Http\Requests\Booking\CreateRendezVousRequest;
use App\Events\RendezVousCreated;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BookingController extends Controller
{
    protected $disponibiliteService;
    protected $oneciService;
    protected $carteResidentService;
    protected $oneciCniService;

    public function __construct(
        DisponibiliteService $disponibiliteService,
        OneciVerificationService $oneciService,
        \App\Services\CarteResidentVerificationService $carteResidentService,
        \App\Services\OneciCniVerificationService $oneciCniService
    ) {
        $this->disponibiliteService = $disponibiliteService;
        $this->oneciService = $oneciService;
        $this->carteResidentService = $carteResidentService;
        $this->oneciCniService = $oneciCniService;
    }

    /**
     * Étape 0: Page de vérification ONECI (nouvelle première étape)
     */
    public function showVerification()
    {
        return view('booking.verification');
    }

    /**
     * Effacer la session ONECI (quand l'utilisateur revient à l'étape service)
     */
    public function clearOneciSession()
    {
        session()->forget([
            'oneci_verified', 'oneci_data', 'oneci_token',
            'carte_resident_verified', 'carte_resident_data', 'verification_type'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Session ONECI effacée'
        ]);
    }

    /**
     * Vérifier le numéro de pré-enrôlement ONECI
     */
    public function verifyPreEnrollment(VerifyPreEnrollmentRequest $request)
    {
        // Validation déjà effectuée par VerifyPreEnrollmentRequest

        try {
            $result = $this->oneciService->verifyPreEnrollmentNumber(
                $request->numero_pre_enrolement
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 404);
            }

            // Vérifier le statut
            if ($result['statut'] !== 'valide') {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'statut' => $result['statut'],
                    'data' => $result['data']
                ], 403);
            }

            // Stocker les informations en session
            session([
                'oneci_verified' => true,
                'oneci_data' => $result['data']
            ]);

            Log::info('Vérification ONECI réussie', [
                'numero' => $request->numero_pre_enrolement,
                'statut' => $result['statut']
            ]);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
                'redirect_url' => route('booking.wizard')
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification ONECI', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la vérification'
            ], 500);
        }
    }

    /**
     * Vérifier le numéro de dossier carte de résident
     */
    public function verifyCarteResident(Request $request)
    {
        $request->validate([
            'numero_dossier' => 'required|string'
        ]);

        try {
            $result = $this->carteResidentService->verifyNumeroDossier(
                $request->numero_dossier
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 404);
            }

            // Vérifier le statut
            if (!$this->carteResidentService->isStatutValide($result['statut'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre dossier n\'est pas encore traité. Statut: ' . $result['statut'],
                    'statut' => $result['statut'],
                    'data' => $result['data']
                ], 403);
            }

            // Stocker les informations en session
            session([
                'carte_resident_verified' => true,
                'carte_resident_data' => $result['data'],
                'verification_type' => 'carte_resident'
            ]);

            Log::info('Vérification carte de résident réussie', [
                'numero_dossier' => $request->numero_dossier,
                'statut' => $result['statut']
            ]);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
                'redirect_url' => route('booking.wizard')
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification carte de résident', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la vérification'
            ], 500);
        }
    }

    /**
     * Vérifier le numéro de dossier CNI (Pré-enrôlement)
     */
    public function verifyPreEnrollmentCni(Request $request)
    {
        $request->validate([
            'numero_dossier' => 'required|string'
        ]);
        
        try {
            // Utiliser le service CNI dédié
            $result = $this->oneciCniService->verifyNumeroDossier(
                $request->numero_dossier
            );

            if (!$result['success']) {
                // Retourner l'erreur avec le statut si disponible (pour affichage précis)
                $data = [
                    'success' => false,
                    'message' => $result['message']
                ];
                if (isset($result['statut_label'])) {
                    $data['statut'] = $result['statut_label'];
                }
                
                return response()->json($data, 404);
            }

            // Si succès, le statut est forcément FPD (vérifié dans le service)
            
            // Stocker les informations en session
            session([
                'oneci_cni_verified' => true,
                'oneci_data' => $result['data'],
                'verification_type' => 'cni'
            ]);

            Log::info('Vérification CNI réussie', [
                'numero_dossier' => $request->numero_dossier,
                'statut' => $result['statut_label']
            ]);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
                'statut' => $result['statut_label'],
                'redirect_url' => route('booking.wizard')
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification CNI', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la vérification'
            ], 500);
        }
    }

    /**
     * Vérifier et valider un token d'accès unique (envoyé par ONECI)
     */
    public function verifyToken($token)
    {
        try {
            $data = $this->oneciService->validateToken($token);

            if (!$data) {
                return redirect()->route('booking.verification')
                    ->with('error', 'Lien invalide ou expiré. Veuillez vérifier votre numéro de pré-enrôlement.');
            }

            // Stocker les informations en session
            session([
                'oneci_verified' => true,
                'oneci_data' => $data['donnees_oneci'],
                'oneci_token' => $token
            ]);

            Log::info('Accès via token ONECI validé', [
                'numero' => $data['numero_pre_enrolement']
            ]);

            return redirect()->route('booking.wizard')
                ->with('success', 'Vérification réussie ! Vous pouvez maintenant prendre rendez-vous.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation du token ONECI', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('booking.verification')
                ->with('error', 'Une erreur est survenue lors de la vérification.');
        }
    }

    /**
     * Redirige vers la plateforme de pré-enrôlement ONECI avec un identifiant unique Mayelia
     */
    public function redirectToOneciPreEnrollment()
    {
        // Générer un identifiant unique pour Mayelia
        $mayeliaId = 'MAYELIA' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        
        // Stocker l'ID en session pour référence future
        session(['mayelia_oneci_id' => $mayeliaId]);
        
        Log::info('Redirection vers ONECI pré-enrôlement', [
            'mayelia_id' => $mayeliaId
        ]);
        
        // Construire l'URL ONECI avec l'identifiant Mayelia
        $oneciUrl = "https://pre-enrolement-cni.oneci.ci/formulaire/{$mayeliaId}";
        
        return redirect()->away($oneciUrl);
    }

    /**
     * Étape 1: Page d'accueil - Sélection du Service (Nouveau Workflow)
     */
    public function index()
    {
        // Récupérer tous les services actifs avec leurs formules (Cache 60min)
        $services = Cache::remember('active_services_with_formules', 60*60, function () {
            return Service::where('statut', 'actif')->with('formules')->get();
        });
        
        return view('booking.index', compact('services'));
    }

    /**
     * Étape 3: Récupérer les pays/villes disponibles pour un service (AJAX)
     */
    public function getLocationsForService($serviceId)
    {
        $locations = Cache::remember('locations_for_service_' . $serviceId, 60*60, function () use ($serviceId) {
            // Récupérer les centres qui proposent ce service
            $centres = Centre::whereHas('services', function($query) use ($serviceId) {
                $query->where('services.id', $serviceId)
                      ->where('centre_services.actif', true);
            })->with('ville')->get();

            // Organiser par pays (pour l'instant on suppose CI, mais structure prête pour multi-pays)
            return [
                'pays' => [
                    'id' => 1,
                    'nom' => 'Côte d\'Ivoire',
                    'code' => 'CI',
                    'villes' => $centres->pluck('ville')->unique('id')->values()
                ]
            ];
        });
        
        return response()->json([
            'success' => true,
            'locations' => [$locations]
        ]);
    }

    /**
     * Étape 3 (suite): Récupérer les centres d'une ville pour un service donné (AJAX)
     */
    public function getCentresForService($villeId, $serviceId)
    {
        $centres = Cache::remember('centres_for_service_' . $villeId . '_' . $serviceId, 60*60, function () use ($villeId, $serviceId) {
            return Centre::where('ville_id', $villeId)
                        ->whereHas('services', function($query) use ($serviceId) {
                            $query->where('services.id', $serviceId)
                                  ->where('centre_services.actif', true);
                        })
                        ->get();
        });
        
        return response()->json([
            'success' => true,
            'centres' => $centres
        ]);
    }

    /**
     * Étape 2: Récupérer les villes d'un pays (AJAX)
     */
    public function getVilles($paysId)
    {
        $villes = Cache::remember('villes_with_centres_' . $paysId, 60*60, function () use ($paysId) {
            return Ville::with('centres')->get();
        });
        
        return response()->json([
            'success' => true,
            'villes' => $villes
        ]);
    }

    /**
     * Étape 3: Récupérer les centres d'une ville (AJAX)
     */
    public function getCentres($villeId)
    {
        $centres = Cache::remember('centres_for_ville_' . $villeId, 60*60, function () use ($villeId) {
            return Centre::where('ville_id', $villeId)
                        ->with('ville')
                        ->get();
        });
        
        return response()->json([
            'success' => true,
            'centres' => $centres
        ]);
    }

    /**
     * Étape 4: Récupérer les services actifs d'un centre (AJAX)
     */
    public function getServices($centreId)
    {
        $services = Cache::remember('services_for_centre_' . $centreId, 60*60, function () use ($centreId) {
            $centre = Centre::findOrFail($centreId);
            return $centre->servicesActives()->with('formules')->get();
        });
        
        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }

    /**
     * Étape 5: Récupérer les formules d'un service pour un centre (AJAX)
     */
    public function getFormules($centreId, $serviceId)
    {
        try {
            $data = Cache::remember('formules_for_centre_' . $centreId . '_service_' . $serviceId, 60*60, function () use ($centreId, $serviceId) {
                $centre = Centre::findOrFail($centreId);
                $service = Service::findOrFail($serviceId);
                
                // Récupérer les formules du service qui sont activées pour ce centre
                $formuleIds = DB::table('centre_formules')
                    ->where('centre_id', $centreId)
                    ->where('actif', true)
                    ->pluck('formule_id');
                
                $formules = Formule::where('service_id', $serviceId)
                    ->where('statut', 'actif')
                    ->whereIn('id', $formuleIds)
                    ->get(['id', 'nom', 'prix', 'service_id']);

                return [
                    'formules' => $formules,
                    'service' => $service
                ];
            });
            
            return response()->json([
                'success' => true,
                'formules' => $data['formules'],
                'service' => $data['service']
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des formules: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des formules: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue'
            ], 500);
        }
    }

    /**
     * Créer un rendez-vous (AJAX)
     */
    public function createRendezVous(CreateRendezVousRequest $request)
    {
        try {
            // Validation déjà effectuée par CreateRendezVousRequest

            // Générer un numéro de suivi unique au format MAYELIA-YYYY-XXXXXX (où XXXXXX sont des chiffres)
            $annee = date('Y');
            $chiffresAleatoires = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $numeroSuivi = 'MAYELIA-' . $annee . '-' . $chiffresAleatoires;

            // Extraire les données ONECI si présentes
            $oneciData = $request->oneci_data;
            $statutOneci = null;
            $tokenVerification = null;
            $numeroPreEnrolement = null;
            $verifiedAt = null;

            if ($oneciData) {
                $statutOneci = 'valide';
                $verifiedAt = now();
                // Essayer de trouver le numéro dans différentes clés
                $numeroPreEnrolement = $oneciData['numero_pre_enrolement'] 
                    ?? $oneciData['numero_dossier'] 
                    ?? null;
                // Générer un token pour lier
                $tokenVerification = md5($numeroSuivi . ($numeroPreEnrolement ?? uniqid()));
            }

            // Créer le rendez-vous
            $rendezVous = RendezVous::create([
                'centre_id' => $request->centre_id,
                'service_id' => $request->service_id,
                'formule_id' => $request->formule_id,
                'client_id' => $request->client_id ?? null, // Peut être null si on n'a pas créé de client en base séparée
                
                // Infos client directes
                'client_nom' => $request->nom,
                'client_prenom' => $request->prenom,
                'client_email' => $request->email,
                'client_telephone' => $request->telephone,
                'date_naissance' => $request->date_naissance,
                'lieu_naissance' => $request->lieu_naissance,
                'sexe' => $request->sexe,
                'adresse' => $request->adresse,
                
                'date_rendez_vous' => $request->date_rendez_vous,
                'tranche_horaire' => $request->tranche_horaire,
                'statut' => RendezVous::STATUT_CONFIRME,
                'numero_suivi' => $numeroSuivi,
                'notes' => $request->notes,
                
                // Champs ONECI
                'numero_pre_enrolement' => $numeroPreEnrolement,
                'token_verification' => $tokenVerification,
                'statut_oneci' => $statutOneci,
                'donnees_oneci' => $oneciData,
                'verified_at' => $verifiedAt
            ]);

            // Charger les relations pour la réponse (client peut être vide)
            $relationships = ['centre', 'service', 'formule'];
            if ($rendezVous->client_id) {
                $relationships[] = 'client';
            }
            $rendezVous->load($relationships);

            // Déclencher l'événement
            event(new RendezVousCreated($rendezVous));

            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous créé avec succès',
                'rendez_vous' => $rendezVous,
                'numero_suivi' => $numeroSuivi
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation lors de la création du rendez-vous', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du rendez-vous', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du rendez-vous'
            ], 500);
        }
    }

    /**
     * Étape 6: Calendrier de disponibilité
     */
    public function calendrier($centreId, $serviceId, $formuleId)
    {
        $centre = Centre::with('ville')->findOrFail($centreId);
        $service = Service::findOrFail($serviceId);
        $formule = Formule::findOrFail($formuleId);
        
        // Vérifier que la formule est bien liée au service et activée pour ce centre
        $centreFormule = $centre->formulesActives()
                               ->where('formule_id', $formuleId)
                               ->where('service_id', $serviceId)
                               ->first();
        
        if (!$centreFormule) {
            return redirect()->route('booking.index')
                           ->with('error', 'Cette formule n\'est pas disponible pour ce centre.');
        }

        return view('booking.calendrier', compact('centre', 'service', 'formule'));
    }

    /**
     * Étape 7: Formulaire client
     */
    public function clientForm($centreId, $serviceId, $formuleId, $date, $tranche)
    {
        $centre = Centre::with('ville')->findOrFail($centreId);
        $service = Service::findOrFail($serviceId);
        $formule = Formule::findOrFail($formuleId);
        
        // Décoder la tranche horaire
        $trancheHoraire = urldecode($tranche);
        
        // Vérifier la disponibilité
        $disponibilite = $this->disponibiliteService->calculerDisponibilite($centreId, $date);
        
        if (!$disponibilite || $disponibilite['statut'] === 'ferme') {
            return redirect()->route('booking.calendrier', [$centreId, $serviceId, $formuleId])
                           ->with('error', 'Ce créneau n\'est plus disponible.');
        }

        return view('booking.client-form', compact(
            'centre', 'service', 'formule', 'date', 'trancheHoraire'
        ));
    }

    /**
     * Étape 8: Traitement du paiement
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'centre_id' => 'required|exists:centres,id',
            'service_id' => 'required|exists:services,id',
            'formule_id' => 'required|exists:formules,id',
            'date_rendez_vous' => 'required|date',
            'tranche_horaire' => 'required|string',
            'client_nom' => 'required|string|max:255',
            'client_prenom' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_telephone' => 'required|string|max:20',
            'client_date_naissance' => 'nullable|date',
            'client_lieu_naissance' => 'nullable|string|max:255',
            'client_adresse' => 'nullable|string|max:500',
            'client_profession' => 'nullable|string|max:255',
            'client_sexe' => 'nullable|in:M,F',
            'client_numero_piece_identite' => 'nullable|string|max:50',
            'client_type_piece_identite' => 'nullable|in:CNI,PASSEPORT,PERMIS',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Créer le client
            $client = Client::create([
                'nom' => $request->client_nom,
                'prenom' => $request->client_prenom,
                'email' => $request->client_email,
                'telephone' => $request->client_telephone,
                'date_naissance' => $request->client_date_naissance,
                'lieu_naissance' => $request->client_lieu_naissance,
                'adresse' => $request->client_adresse,
                'profession' => $request->client_profession,
                'sexe' => $request->client_sexe,
                'numero_piece_identite' => $request->client_numero_piece_identite,
                'type_piece_identite' => $request->client_type_piece_identite,
                'notes' => $request->client_notes,
                'actif' => true,
            ]);

            // Créer le rendez-vous
            $rendezVous = RendezVous::create([
                'centre_id' => $request->centre_id,
                'service_id' => $request->service_id,
                'formule_id' => $request->formule_id,
                'client_id' => $client->id,
                'date_rendez_vous' => $request->date_rendez_vous,
                'tranche_horaire' => $request->tranche_horaire,
                'statut' => RendezVous::STATUT_CONFIRME,
                'notes' => 'Réservation en ligne',
            ]);

            // Générer un numéro de suivi au format MAYELIA-YYYY-XXXXXX (où XXXXXX sont des chiffres)
            $annee = date('Y');
            $chiffresAleatoires = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $numeroSuivi = 'MAYELIA-' . $annee . '-' . $chiffresAleatoires;
            $rendezVous->update(['numero_suivi' => $numeroSuivi]);

            // Déclencher l'événement
            event(new RendezVousCreated($rendezVous->fresh()));

            return redirect()->route('booking.confirmation', $rendezVous->id)
                           ->with('success', 'Rendez-vous créé avec succès !');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du rendez-vous: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Une erreur est survenue lors de la création du rendez-vous.');
        }
    }

    /**
     * Étape 9: Confirmation et reçu
     */
    public function confirmation($rendezVousId)
    {
        $rendezVous = RendezVous::withRelations()
                               ->findOrFail($rendezVousId);

        return view('booking.confirmation', compact('rendezVous'));
    }

    /**
     * Téléchargement du reçu en PDF
     */
    public function downloadReceipt($rendezVousId)
    {
        $rendezVous = RendezVous::withRelations()
                               ->findOrFail($rendezVousId);

        // Générer le QR code en SVG (ne nécessite pas ImageMagick)
        $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)
            ->format('svg')
            ->generate($rendezVous->numero_suivi);

        // Convertir en base64 pour l'affichage dans le PDF
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // Générer le PDF du reçu
        $pdf = Pdf::loadView('booking.receipt', compact('rendezVous', 'qrCodeBase64'));
        
        // Configuration du PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        // Nom du fichier
        $filename = 'Reçu_' . $rendezVous->numero_suivi . '_' . now()->format('Y-m-d') . '.pdf';

        // Télécharger le PDF
        return $pdf->download($filename);
    }
}