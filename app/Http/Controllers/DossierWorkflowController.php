<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\DossierOuvert;
use App\Models\DocumentVerification;
use App\Models\PaiementVerification;
use App\Models\DocumentRequis;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DossierWorkflowController extends Controller
{
    /**
     * Afficher la liste des rendez-vous pour les agents
     */
    public function index(Request $request)
    {
        $agent = Auth::user();
        
        // Récupérer les rendez-vous du centre de l'agent
        $query = RendezVous::with(['client', 'service', 'formule', 'centre', 'dossierOuvert.agent'])
            ->where('centre_id', $agent->centre_id);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date')) {
            $query->whereDate('date_rendez_vous', $request->date);
        }

        $rendezVous = $query->orderBy('date_rendez_vous', 'desc')
                           ->orderBy('tranche_horaire', 'asc')
                           ->paginate(15);

        return view('agent.rendez-vous.index', compact('rendezVous'));
    }

    /**
     * Ouvrir un dossier
     */
    public function openDossier(Request $request, RendezVous $rendezVous)
    {
        Log::info('=== DÉBUT OUVERTURE DOSSIER ===');
        Log::info('Request data:', $request->all());
        
        try {
            $agent = Auth::user();
            
            Log::info('Utilisateur connecté:', [
                'user_id' => $agent ? $agent->id : 'NULL',
                'user_nom' => $agent ? $agent->nom : 'NULL',
                'user_email' => $agent ? $agent->email : 'NULL',
                'user_centre_id' => $agent ? $agent->centre_id : 'NULL',
                'user_role' => $agent ? $agent->role : 'NULL'
            ]);
            
            // Debug: Vérifier l'utilisateur connecté
            if (!$agent) {
                Log::error('ERREUR: Utilisateur non connecté');
                return response()->json(['success' => false, 'message' => 'Utilisateur non connecté'], 401);
            }
            
            Log::info('Rendez-vous cible:', [
                'rdv_id' => $rendezVous->id,
                'rdv_centre_id' => $rendezVous->centre_id,
                'rdv_statut' => $rendezVous->statut,
                'rdv_client_id' => $rendezVous->client_id,
                'rdv_date' => $rendezVous->date_rendez_vous
            ]);

            // Vérifier que l'agent a un centre_id
            if (!$agent->centre_id) {
                Log::error('ERREUR: Agent sans centre_id', [
                    'agent_id' => $agent->id,
                    'agent_nom' => $agent->nom,
                    'agent_centre_id' => $agent->centre_id
                ]);
                return response()->json(['success' => false, 'message' => 'Agent non assigné à un centre'], 400);
            }
            Log::info('✓ Agent a un centre_id:', ['centre_id' => $agent->centre_id]);

            // Vérifier que le RDV appartient au centre de l'agent
            if ($rendezVous->centre_id !== $agent->centre_id) {
                Log::warning('ERREUR: Tentative d\'accès non autorisé', [
                    'agent_centre_id' => $agent->centre_id,
                    'rdv_centre_id' => $rendezVous->centre_id,
                    'comparaison' => $rendezVous->centre_id . ' !== ' . $agent->centre_id
                ]);
                return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
            }
            Log::info('✓ Centres correspondent:', [
                'agent_centre' => $agent->centre_id,
                'rdv_centre' => $rendezVous->centre_id
            ]);

            // Vérifier que le RDV est confirmé
            if ($rendezVous->statut !== 'confirme') {
                Log::warning('ERREUR: RDV non confirmé', [
                    'rdv_statut' => $rendezVous->statut,
                    'statut_attendu' => 'confirme'
                ]);
                return response()->json(['success' => false, 'message' => 'Ce rendez-vous ne peut pas être ouvert'], 400);
            }
            Log::info('✓ RDV est confirmé');

            // Vérifier qu'il n'y a pas déjà un dossier ouvert
            $existingDossier = $rendezVous->dossierOuvert;
            if ($existingDossier) {
                Log::warning('ERREUR: Dossier déjà ouvert', [
                    'dossier_id' => $existingDossier->id,
                    'agent_dossier' => $existingDossier->agent_id,
                    'date_ouverture' => $existingDossier->date_ouverture
                ]);
                return response()->json(['success' => false, 'message' => 'Un dossier est déjà ouvert pour ce rendez-vous'], 400);
            }
            Log::info('✓ Aucun dossier existant');

            // Créer le dossier ouvert
            Log::info('Création du dossier ouvert...', [
                'data' => [
                    'rendez_vous_id' => $rendezVous->id,
                    'agent_id' => $agent->id,
                    'date_ouverture' => now(),
                    'statut' => 'ouvert',
                    'notes' => $request->input('notes')
                ]
            ]);
            
            $dossierOuvert = DossierOuvert::create([
                'rendez_vous_id' => $rendezVous->id,
                'agent_id' => $agent->id,
                'date_ouverture' => now(),
                'statut' => 'ouvert',
                'notes' => $request->input('notes')
            ]);
            
            Log::info('✓ Dossier créé avec succès', [
                'dossier_id' => $dossierOuvert->id,
                'dossier_statut' => $dossierOuvert->statut
            ]);

            // Mettre à jour le statut du rendez-vous
            Log::info('Mise à jour du statut du RDV...');
            $rendezVous->update(['statut' => 'dossier_ouvert']);
            Log::info('✓ Statut RDV mis à jour:', ['nouveau_statut' => $rendezVous->fresh()->statut]);

            Log::info('=== SUCCÈS OUVERTURE DOSSIER ===', [
                'dossier_id' => $dossierOuvert->id,
                'rendez_vous_id' => $rendezVous->id,
                'agent_id' => $agent->id
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Dossier ouvert avec succès',
                'dossier_id' => $dossierOuvert->id,
                'redirect_url' => route('dossier.workflow', $dossierOuvert->id)
            ]);

        } catch (\Exception $e) {
            Log::error('=== ERREUR OUVERTURE DOSSIER ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'rendez_vous_id' => $rendezVous->id ?? 'unknown'
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'ouverture du dossier: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Afficher le workflow d'un dossier
     */
    public function showWorkflow(DossierOuvert $dossierOuvert)
    {
        $agent = Auth::user();

        // Vérifier que l'agent peut gérer ce dossier
        if (!$dossierOuvert->canBeManagedBy($agent)) {
            abort(403, 'Vous ne pouvez pas gérer ce dossier');
        }

        $dossierOuvert->load([
            'rendezVous.client',
            'rendezVous.service',
            'rendezVous.formule',
            'rendezVous.centre',
            'documentVerifications.documentRequis',
            'paiementVerification'
        ]);

        // Récupérer les documents requis pour le service
        $documentsRequis = DocumentRequis::where('service_id', $dossierOuvert->rendezVous->service_id)
            ->orderBy('ordre')
            ->get();

        // Récupérer les résultats des documents vérifiés
        $documentsVerifies = [];
        $typeDemandeVerifie = null;
        
        if ($dossierOuvert->documents_verifies || $dossierOuvert->documents_manquants) {
            // Récupérer le type de demande le plus récent vérifié
            $derniereVerification = $dossierOuvert->documentVerifications()
                ->with('documentRequis')
                ->latest('date_verification')
                ->first();
                
            if ($derniereVerification) {
                $typeDemandeVerifie = $derniereVerification->documentRequis->type_demande;
                
                // Récupérer tous les documents vérifiés pour ce type de demande
                $verifications = $dossierOuvert->documentVerifications()
                    ->with('documentRequis')
                    ->whereHas('documentRequis', function($query) use ($typeDemandeVerifie) {
                        $query->where('type_demande', $typeDemandeVerifie);
                    })
                    ->get();
                
                $documentsSelectionnes = [];
                $documentsManquants = [];
                
                foreach ($verifications as $verification) {
                    $doc = [
                        'id' => $verification->documentRequis->id,
                        'nom' => $verification->documentRequis->nom_document,
                        'obligatoire' => $verification->documentRequis->obligatoire
                    ];
                    
                    if ($verification->present) {
                        $documentsSelectionnes[] = $doc;
                    } else {
                        $documentsManquants[] = $doc;
                    }
                }
                
                $documentsVerifies = [
                    'type_demande' => $typeDemandeVerifie,
                    'documents_selectionnes' => $documentsSelectionnes,
                    'documents_manquants' => $documentsManquants
                ];
            }
        }

        return view('agent.dossier.workflow', compact('dossierOuvert', 'documentsRequis', 'documentsVerifies'));
    }

    /**
     * Valider l'étape 1: Fiche pré-enrôlement
     */
    public function validerEtape1(Request $request, DossierOuvert $dossierOuvert)
    {
        try {
            $agent = Auth::user();
            
            // Vérifier que l'agent peut gérer ce dossier
            if (!$dossierOuvert->canBeManagedBy($agent)) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
            }

            // Mettre à jour le statut de l'étape 1
            $dossierOuvert->update([
                'fiche_pre_enrolement_verifiee' => true,
                'notes' => $request->input('commentaires', $dossierOuvert->notes)
            ]);

            // Mettre à jour le statut du dossier
            $this->updateDossierStatus($dossierOuvert);

            return response()->json([
                'success' => true,
                'message' => 'Fiche pré-enrôlement validée avec succès',
                'progression' => $dossierOuvert->fresh()->progression
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur validation étape 1: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la validation'], 500);
        }
    }

    /**
     * Valider l'étape 2: Documents
     */
    public function validerEtape2(Request $request, DossierOuvert $dossierOuvert)
    {
        try {
            $agent = Auth::user();
            
            if (!$dossierOuvert->canBeManagedBy($agent)) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
            }

            $typeDemande = $request->input('type_demande');
            $documentsCoches = $request->input('documents', []);
            
            if (!$typeDemande) {
                return response()->json(['success' => false, 'message' => 'Type de demande requis'], 400);
            }
            
            // Récupérer les documents requis pour ce service et ce type de demande
            $documentsRequis = DocumentRequis::where('service_id', $dossierOuvert->rendezVous->service_id)
                ->where('type_demande', $typeDemande)
                ->get();
            
            if ($documentsRequis->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Aucun document requis trouvé pour ce type de demande'], 400);
            }
            
            $documentsManquants = false;
            $documentsSelectionnes = [];
            $documentsManquantsList = [];
            
            // Créer les vérifications de documents
            foreach ($documentsRequis as $document) {
                $present = false;
                foreach ($documentsCoches as $docCoche) {
                    if (is_array($docCoche) && $docCoche['id'] == $document->id) {
                        $present = true;
                        break;
                    } elseif (is_numeric($docCoche) && $docCoche == $document->id) {
                        $present = true;
                        break;
                    }
                }
                
                DocumentVerification::updateOrCreate(
                    [
                        'dossier_ouvert_id' => $dossierOuvert->id,
                        'document_requis_id' => $document->id
                    ],
                    [
                        'present' => $present,
                        'verifie_par' => $agent->id,
                        'date_verification' => now()
                    ]
                );
                
                if ($present) {
                    $documentsSelectionnes[] = [
                        'id' => $document->id,
                        'nom' => $document->nom_document,
                        'obligatoire' => $document->obligatoire
                    ];
                } else {
                    $documentsManquantsList[] = [
                        'id' => $document->id,
                        'nom' => $document->nom_document,
                        'obligatoire' => $document->obligatoire
                    ];
                    
                    // Vérifier si un document obligatoire est manquant
                    if ($document->obligatoire) {
                        $documentsManquants = true;
                    }
                }
            }

            // Mettre à jour le statut des documents
            $dossierOuvert->update([
                'documents_verifies' => !$documentsManquants,
                'documents_manquants' => $documentsManquants
            ]);

            $this->updateDossierStatus($dossierOuvert);

            return response()->json([
                'success' => true,
                'message' => $documentsManquants ? 'Documents vérifiés (certains manquants)' : 'Documents vérifiés avec succès',
                'progression' => $dossierOuvert->fresh()->progression,
                'documents_selectionnes' => $documentsSelectionnes,
                'documents_manquants' => $documentsManquantsList,
                'type_demande' => $typeDemande
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur validation étape 2: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la validation'], 500);
        }
    }

    /**
     * Valider l'étape 3: Informations client
     */
    public function validerEtape3(Request $request, DossierOuvert $dossierOuvert)
    {
        try {
            $agent = Auth::user();
            
            if (!$dossierOuvert->canBeManagedBy($agent)) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
            }

            // Si R.A.S, on valide directement sans modification
            if ($request->input('ras')) {
                // Marquer l'étape 3 comme complétée
                Log::info('Mise à jour R.A.S - Avant:', ['informations_client_verifiees' => $dossierOuvert->informations_client_verifiees]);
                $dossierOuvert->update(['informations_client_verifiees' => true]);
                Log::info('Mise à jour R.A.S - Après:', ['informations_client_verifiees' => $dossierOuvert->fresh()->informations_client_verifiees]);
                $this->updateDossierStatus($dossierOuvert);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Informations client validées (R.A.S)',
                    'progression' => $dossierOuvert->fresh()->progression
                ]);
            }

            $client = $dossierOuvert->rendezVous->client;
            
            // Mettre à jour les informations du client
            $client->update([
                'nom' => $request->input('nom', $client->nom),
                'prenom' => $request->input('prenom', $client->prenom),
                'email' => $request->input('email', $client->email),
                'telephone' => $request->input('telephone', $client->telephone),
                'date_naissance' => $request->input('date_naissance', $client->date_naissance),
                'numero_piece_identite' => $request->input('cni', $client->numero_piece_identite)
            ]);

            // Marquer l'étape 3 comme complétée
            Log::info('Mise à jour client - Avant:', ['informations_client_verifiees' => $dossierOuvert->informations_client_verifiees]);
            $dossierOuvert->update(['informations_client_verifiees' => true]);
            Log::info('Mise à jour client - Après:', ['informations_client_verifiees' => $dossierOuvert->fresh()->informations_client_verifiees]);
            $this->updateDossierStatus($dossierOuvert);

            return response()->json([
                'success' => true,
                'message' => 'Informations client mises à jour avec succès',
                'progression' => $dossierOuvert->fresh()->progression
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur validation étape 3: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la validation'], 500);
        }
    }

    /**
     * Valider l'étape 4: Paiement
     */
    public function validerEtape4(Request $request, DossierOuvert $dossierOuvert)
    {
        try {
            $agent = Auth::user();
            
            if (!$dossierOuvert->canBeManagedBy($agent)) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
            }

            // Créer la vérification de paiement
            PaiementVerification::create([
                'dossier_ouvert_id' => $dossierOuvert->id,
                'montant_paye' => $request->input('montant'),
                'date_paiement' => now(),
                'mode_paiement' => $request->input('mode_paiement'),
                'reference_paiement' => $request->input('reference'),
                'verifie_par' => $agent->id,
                'date_verification' => now()
            ]);

            // Mettre à jour le statut du paiement
            $dossierOuvert->update([
                'paiement_verifie' => true
            ]);

            // Mettre à jour le statut du rendez-vous
            $dossierOuvert->rendezVous->update([
                'statut' => 'paiement_effectue'
            ]);

            $this->updateDossierStatus($dossierOuvert);

            return response()->json([
                'success' => true,
                'message' => 'Paiement validé avec succès',
                'progression' => $dossierOuvert->fresh()->progression
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur validation étape 4: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la validation'], 500);
        }
    }

    /**
     * Mettre à jour le statut du dossier selon les étapes complétées
     */
    private function updateDossierStatus(DossierOuvert $dossierOuvert)
    {
        $etapes = [
            $dossierOuvert->fiche_pre_enrolement_verifiee,
            $dossierOuvert->documents_verifies,
            true, // Informations client toujours considérées comme validées
            $dossierOuvert->paiement_verifie
        ];

        $etapesCompletes = count(array_filter($etapes));

        if ($etapesCompletes === 4) {
            $dossierOuvert->update(['statut' => 'finalise']);
        } elseif ($etapesCompletes > 0) {
            $dossierOuvert->update(['statut' => 'en_cours']);
        }
    }
}