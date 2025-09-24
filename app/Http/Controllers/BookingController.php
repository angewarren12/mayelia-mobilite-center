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
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    protected $disponibiliteService;

    public function __construct(DisponibiliteService $disponibiliteService)
    {
        $this->disponibiliteService = $disponibiliteService;
    }

    /**
     * Étape 1: Page d'accueil - Sélection du pays
     */
    public function index()
    {
        // Pour l'instant, on assume qu'on est en Côte d'Ivoire
        // Plus tard, on pourra ajouter une table pays
        $pays = [
            'id' => 1,
            'nom' => 'Côte d\'Ivoire',
            'code' => 'CI'
        ];

        return view('booking.index', compact('pays'));
    }

    /**
     * Étape 2: Récupérer les villes d'un pays (AJAX)
     */
    public function getVilles($paysId)
    {
        $villes = Ville::with('centres')->get();
        
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
        $centres = Centre::where('ville_id', $villeId)
                        ->with('ville')
                        ->get();
        
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
        $centre = Centre::findOrFail($centreId);
        $services = $centre->servicesActives()->with('formules')->get();
        
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
        $centre = Centre::findOrFail($centreId);
        $service = Service::findOrFail($serviceId);
        
        // Récupérer les formules liées à ce service et activées pour ce centre
        $formules = $centre->formulesActives()
                          ->where('service_id', $serviceId)
                          ->get();
        
        return response()->json([
            'success' => true,
            'formules' => $formules,
            'service' => $service
        ]);
    }

    /**
     * Créer un rendez-vous (AJAX)
     */
    public function createRendezVous(Request $request)
    {
        try {
            // Validation des données
            $request->validate([
                'centre_id' => 'required|exists:centres,id',
                'service_id' => 'required|exists:services,id',
                'formule_id' => 'required|exists:formules,id',
                'client_id' => 'required|exists:clients,id',
                'date_rendez_vous' => 'required|date',
                'tranche_horaire' => 'required|string',
                'notes' => 'nullable|string|max:1000'
            ]);

            // Générer un numéro de suivi unique
            $numeroSuivi = 'RDV-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

            // Créer le rendez-vous
            $rendezVous = RendezVous::create([
                'centre_id' => $request->centre_id,
                'service_id' => $request->service_id,
                'formule_id' => $request->formule_id,
                'client_id' => $request->client_id,
                'date_rendez_vous' => $request->date_rendez_vous,
                'tranche_horaire' => $request->tranche_horaire,
                'statut' => 'confirme',
                'numero_suivi' => $numeroSuivi,
                'notes' => $request->notes
            ]);

            // Charger les relations pour la réponse
            $rendezVous->load(['centre', 'service', 'formule', 'client']);

            \Log::info('Rendez-vous créé avec succès', [
                'rendez_vous_id' => $rendezVous->id,
                'numero_suivi' => $numeroSuivi,
                'client' => $rendezVous->client->nom . ' ' . $rendezVous->client->prenom,
                'centre' => $rendezVous->centre->nom,
                'date' => $rendezVous->date_rendez_vous
            ]);

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
                'statut' => 'confirme',
                'notes' => 'Réservation en ligne',
            ]);

            // Générer un numéro de suivi
            $numeroSuivi = 'RDV-' . strtoupper(substr(md5($rendezVous->id . time()), 0, 8));
            $rendezVous->update(['numero_suivi' => $numeroSuivi]);

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
        $rendezVous = RendezVous::with(['centre.ville', 'service', 'formule', 'client'])
                               ->findOrFail($rendezVousId);

        return view('booking.confirmation', compact('rendezVous'));
    }

    /**
     * Téléchargement du reçu en PDF
     */
    public function downloadReceipt($rendezVousId)
    {
        $rendezVous = RendezVous::with(['centre.ville', 'service', 'formule', 'client'])
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