<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\DossierOuvert;
use App\Models\DocumentVerification;
use App\Models\PaiementVerification;
use App\Models\DocumentRequis;
use App\Models\Client;
use App\Events\DossierOpened;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\AuthService;
use App\Http\Controllers\Concerns\ChecksPermissions;

class DossierWorkflowController extends Controller
{
    use ChecksPermissions;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
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
            if ($rendezVous->statut !== RendezVous::STATUT_CONFIRME) {
                Log::warning('ERREUR: RDV non confirmé', [
                    'rdv_statut' => $rendezVous->statut,
                    'statut_attendu' => RendezVous::STATUT_CONFIRME
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
            
            $dossierOuvert->logAction('ouvert', 'Ouverture du dossier via workflow');
            
            Log::info('✓ Dossier créé avec succès', [
                'dossier_id' => $dossierOuvert->id,
                'dossier_statut' => $dossierOuvert->statut
            ]);

            // Déclencher l'événement (le listener mettra à jour le statut du RDV)
            event(new DossierOpened($dossierOuvert));

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

            $updateData = [
                'fiche_pre_enrolement_verifiee' => true,
                'notes' => $request->input('commentaires', $dossierOuvert->notes)
            ];

            // Gérer l'upload de la fiche (optionnel)
            if ($request->hasFile('fiche_file')) {
                $file = $request->file('fiche_file');
                
                // Valider le fichier
                $request->validate([
                    'fiche_file' => 'file|mimes:pdf,jpg,jpeg,png|max:10240'
                ]);
                
                // Stocker le fichier
                $filename = 'fiche_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('dossiers/' . $dossierOuvert->id . '/fiche', $filename, 'public');
                
                $updateData['fiche_pre_enrolement_path'] = $path;
                
                Log::info('Fiche pré-enrôlement uploadée:', ['path' => $path]);
            }

            // Mettre à jour le dossier
            $dossierOuvert->update($updateData);
            
            $dossierOuvert->logAction('fiche_verifiee', 'Validation de la fiche de pré-enrôlement', [
                'commentaire' => $request->input('commentaires'),
                'avec_fichier' => isset($updateData['fiche_pre_enrolement_path'])
            ]);

            // Mettre à jour le statut du dossier
            $this->updateDossierStatus($dossierOuvert);
            
            // Recharger le dossier pour obtenir la progression à jour
            $dossierOuvert->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Fiche pré-enrôlement validée avec succès',
                'progression' => $dossierOuvert->progression
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
                $fichierData = [];
                
                // Vérifier si le document est coché
                foreach ($documentsCoches as $docCoche) {
                    if (is_array($docCoche) && $docCoche['id'] == $document->id) {
                        $present = true;
                        break;
                    } elseif (is_numeric($docCoche) && $docCoche == $document->id) {
                        $present = true;
                        break;
                    }
                }
                
                // Gérer l'upload du fichier si présent
                if ($present && $request->hasFile("documents.{$document->id}.fichier")) {
                    $file = $request->file("documents.{$document->id}.fichier");
                    
                    // Valider le fichier
                    $request->validate([
                        "documents.{$document->id}.fichier" => 'file|mimes:pdf,jpg,jpeg,png|max:10240' // 10MB max
                    ]);
                    
                    // Stocker le fichier
                    $filename = time() . '_' . $document->id . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('dossiers/' . $dossierOuvert->id . '/documents', $filename, 'public');
                    
                    $fichierData = [
                        'nom_fichier' => $filename,
                        'chemin_fichier' => $path,
                        'taille_fichier' => $file->getSize(),
                        'type_mime' => $file->getMimeType()
                    ];
                }
                
                // Créer ou mettre à jour la vérification
                DocumentVerification::updateOrCreate(
                    [
                        'dossier_ouvert_id' => $dossierOuvert->id,
                        'document_requis_id' => $document->id
                    ],
                    array_merge([
                        'present' => $present,
                        'verifie_par' => $agent->id,
                        'date_verification' => now()
                    ], $fichierData)
                );
                
                if ($present) {
                    $documentsSelectionnes[] = [
                        'id' => $document->id,
                        'nom' => $document->nom_document,
                        'obligatoire' => $document->obligatoire,
                        'fichier_uploade' => !empty($fichierData)
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
            
            $action = $documentsManquants ? 'documents_incomplets' : 'documents_verifies';
            $description = $documentsManquants ? 'Documents vérifiés avec des manquants' : 'Tous les documents ont été vérifiés';
            
            $dossierOuvert->logAction($action, $description, [
                'type_demande' => $typeDemande,
                'documents_manquants' => $documentsManquantsList,
                'documents_uploades' => count(array_filter($documentsSelectionnes, fn($d) => $d['fichier_uploade']))
            ]);

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
                
                $dossierOuvert->logAction('infos_client_verifiees', 'Informations client validées (R.A.S)');
                
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
                'adresse' => $request->input('adresse', $client->adresse),
                'profession' => $request->input('profession', $client->profession)
            ]);

            // Marquer l'étape 3 comme complétée
            Log::info('Mise à jour client - Avant:', ['informations_client_verifiees' => $dossierOuvert->informations_client_verifiees]);
            $dossierOuvert->update(['informations_client_verifiees' => true]);
            Log::info('Mise à jour client - Après:', ['informations_client_verifiees' => $dossierOuvert->fresh()->informations_client_verifiees]);
            $this->updateDossierStatus($dossierOuvert);

            $dossierOuvert->logAction('infos_client_maj', 'Informations client mises à jour et validées');

            // Recharger le client pour obtenir les données à jour
            $client->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Informations client mises à jour avec succès',
                'progression' => $dossierOuvert->fresh()->progression,
                'client' => [
                    'nom' => $client->nom,
                    'prenom' => $client->prenom,
                    'email' => $client->email,
                    'telephone' => $client->telephone,
                    'date_naissance' => $client->date_naissance,
                    'adresse' => $client->adresse,
                    'profession' => $client->profession
                ]
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

            // Gérer le reçu de paiement (optionnel mais recommandé)
            $recuPath = null;
            if ($request->hasFile('recu_file')) {
                $file = $request->file('recu_file');
                
                // Valider le fichier
                $request->validate([
                    'recu_file' => 'file|mimes:pdf,jpg,jpeg,png|max:10240'
                ]);
                
                // Stocker le fichier
                $filename = 'recu_paiement_' . time() . '_' . $file->getClientOriginalName();
                $recuPath = $file->storeAs('dossiers/' . $dossierOuvert->id . '/paiement', $filename, 'public');
            }

            // Préparer les données de paiement
            $paiementData = [
                'montant_paye' => $request->input('montant'),
                'date_paiement' => now(),
                'reference_paiement' => $request->input('reference'),
                'verifie_par' => $agent->id,
                'date_verification' => now()
            ];

            if ($recuPath) {
                $paiementData['recu_tracabilite_path'] = $recuPath;
            }

            // Créer ou mettre à jour la vérification de paiement
            PaiementVerification::updateOrCreate(
                ['dossier_ouvert_id' => $dossierOuvert->id],
                $paiementData
            );

            // Mettre à jour le statut du paiement
            $dossierOuvert->update([
                'paiement_verifie' => true
            ]);

            // Mettre à jour le statut du rendez-vous s'il existe
            if ($dossierOuvert->rendezVous) {
                $dossierOuvert->rendezVous->update([
                    'statut' => 'paiement_effectue'
                ]);
            }

            $this->updateDossierStatus($dossierOuvert);
            
            $dossierOuvert->logAction('paiement_verifie', 'Paiement validé', [
                'montant' => $request->input('montant'),
                'reference' => $request->input('reference'),
                'avec_recu' => !is_null($recuPath)
            ]);

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

    // ...

    /**
     * Rejeter un dossier
     */
    public function rejeter(Request $request, DossierOuvert $dossierOuvert)
    {
        // Vérifier la permission
        // $this->checkPermission('dossiers', 'update');

        try {
            $agent = $this->authService->getAuthenticatedUser();

            // Vérifier que l'agent peut gérer ce dossier
            if (!$dossierOuvert->canBeManagedBy($agent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas gérer ce dossier'
                ], 403);
            }

            // Vérifier que le dossier n'est pas déjà finalisé
            if ($dossierOuvert->statut === 'finalise') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de rejeter un dossier déjà finalisé'
                ], 400);
            }

            // Vérifier que le dossier n'est pas déjà annulé
            if ($dossierOuvert->statut === 'annulé') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce dossier est déjà rejeté'
                ], 400);
            }

            // Valider la note de rejet
            $request->validate([
                'note' => 'required|string|min:10|max:1000'
            ], [
                'note.required' => 'Veuillez indiquer le motif du rejet',
                'note.min' => 'Le motif doit contenir au moins 10 caractères',
                'note.max' => 'Le motif ne peut pas dépasser 1000 caractères'
            ]);

            // Mettre à jour les notes du dossier (ajouter la note de rejet)
            $notes = $dossierOuvert->notes ? $dossierOuvert->notes . "\n\n[REJET] " . now()->format('d/m/Y H:i') . " - " . $request->input('note') : "[REJET] " . now()->format('d/m/Y H:i') . " - " . $request->input('note');

            // Rejeter le dossier (statut = annulé)
            $dossierOuvert->update([
                'statut' => 'annulé',
                'notes' => $notes
            ]);

            // Mettre à jour le statut du rendez-vous s'il existe
            if ($dossierOuvert->rendezVous) {
                $dossierOuvert->rendezVous->update([
                    'statut' => 'annule'
                ]);
            }

            // Logger l'action
            $dossierOuvert->logAction('rejete', 'Dossier rejeté', [
                'motif' => $request->input('note')
            ]);

            Log::info('Dossier rejeté', [
                'dossier_id' => $dossierOuvert->id,
                'agent_id' => $agent->id,
                'motif' => $request->input('note'),
                'has_rendez_vous' => $dossierOuvert->rendezVous !== null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Le dossier a été rejeté avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors du rejet du dossier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet du dossier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finaliser un dossier
     */
    public function finaliser(DossierOuvert $dossierOuvert)
    {
        // Vérifier la permission (pas de delete pour les agents)
        // $this->checkPermission('dossiers', 'update');

        try {
            $agent = $this->authService->getAuthenticatedUser();

            // Vérifier que l'agent peut gérer ce dossier
            if (!$dossierOuvert->canBeManagedBy($agent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas gérer ce dossier'
                ], 403);
            }

            // Vérifier que toutes les étapes sont validées
            if (!$dossierOuvert->fiche_pre_enrolement_verifiee) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fiche de pré-enrôlement n\'est pas encore validée'
                ], 400);
            }

            if (!$dossierOuvert->documents_verifies) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les documents ne sont pas encore validés'
                ], 400);
            }

            if (!$dossierOuvert->informations_client_verifiees) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les informations client ne sont pas encore validées'
                ], 400);
            }

            if (!$dossierOuvert->paiement_verifie) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le paiement n\'est pas encore validé'
                ], 400);
            }

            // Finaliser le dossier
            $dossierOuvert->update([
                'statut' => 'finalise',
                'date_finalisation' => now()
            ]);

            // Mettre à jour le statut du rendez-vous s'il existe
            if ($dossierOuvert->rendezVous) {
                $dossierOuvert->rendezVous->update([
                    'statut' => 'finalise'
                ]);
            }

            $dossierOuvert->logAction('finalise', 'Dossier finalisé');

            Log::info('Dossier finalisé avec succès', [
                'dossier_id' => $dossierOuvert->id,
                'agent_id' => $agent->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Le dossier a été finalisé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la finalisation du dossier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la finalisation du dossier'
            ], 500);
        }
    }

    /**
     * Afficher la page de workflow d'un dossier
     */
    public function showWorkflow(DossierOuvert $dossierOuvert)
    {
        $agent = Auth::user();
        
        // Charger les relations nécessaires (rendezVous peut être null)
        $dossierOuvert->load([
            'agent',
            'documentVerifications.documentRequis',
            'paiementVerification',
            'actionsLog.user'
        ]);
        
        // Charger les relations du rendez-vous seulement s'il existe
        if ($dossierOuvert->rendez_vous_id) {
            $dossierOuvert->load([
                'rendezVous.client',
                'rendezVous.centre',
                'rendezVous.service',
                'rendezVous.formule',
            ]);
        }
        
        // Vérifier que l'agent peut gérer ce dossier
        if (!$dossierOuvert->canBeManagedBy($agent)) {
            abort(403, 'Vous ne pouvez pas gérer ce dossier');
        }
        
        // Récupérer les documents requis pour ce service (si rendez-vous existe)
        $documentsRequis = collect([]);
        if ($dossierOuvert->rendezVous && $dossierOuvert->rendezVous->service_id) {
            $documentsRequis = DocumentRequis::where('service_id', $dossierOuvert->rendezVous->service_id)
                ->orderBy('type_demande')
                ->orderBy('ordre')
                ->get();
        }
        
        // Récupérer les documents déjà vérifiés
        $documentsVerifies = $dossierOuvert->documentVerifications()
            ->with('documentRequis')
            ->get()
            ->keyBy('document_requis_id');
        
        return view('agent.dossier.workflow', compact('dossierOuvert', 'documentsRequis', 'documentsVerifies'));
    }

    /**
     * Imprimer le reçu de traçabilité
     */
    public function imprimerRecu(DossierOuvert $dossierOuvert)
    {
        // Charger les relations nécessaires
        $dossierOuvert->load([
            'rendezVous.client',
            'rendezVous.centre.ville',
            'rendezVous.service',
            'rendezVous.formule',
            'agent',
            'paiementVerification'
        ]);
        
        // Vérifier que le dossier est finalisé
        if ($dossierOuvert->statut !== 'finalise') {
            abort(403, 'Le dossier doit être finalisé pour imprimer le reçu');
        }
        
        // Générer le PDF
        $pdf = Pdf::loadView('agent.dossier.recu-pdf', compact('dossierOuvert'));
        
        $filename = 'recu-mayelia-dossier-' . $dossierOuvert->id . '-' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Imprimer l'étiquette avec code-barres
     */
    public function imprimerEtiquette(DossierOuvert $dossierOuvert)
    {
        // Charger les relations nécessaires
        $dossierOuvert->load([
            'rendezVous.client',
            'rendezVous.centre',
            'rendezVous.service',
            'agent'
        ]);
        
        // Vérifier que le dossier est finalisé
        if ($dossierOuvert->statut !== 'finalise') {
            abort(403, 'Le dossier doit être finalisé pour imprimer l\'étiquette');
        }
        
        // Générer le code-barres si non existant
        if (!$dossierOuvert->code_barre) {
            $dossierOuvert->update([
                'code_barre' => 'MAY-' . str_pad($dossierOuvert->id, 8, '0', STR_PAD_LEFT)
            ]);
        }
        
        // Générer le PDF
        $pdf = Pdf::loadView('agent.dossier.etiquette', compact('dossierOuvert'));
        
        // Format A6 pour étiquette (105 x 148 mm)
        $pdf->setPaper([0, 0, 297.64, 419.53], 'portrait'); // A6 en points
        
        $filename = 'etiquette-dossier-' . $dossierOuvert->id . '-' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Mettre à jour le statut global du dossier
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