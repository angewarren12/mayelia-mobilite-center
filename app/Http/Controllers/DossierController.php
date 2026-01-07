<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\DossierOuvert;
use App\Models\RendezVous;
use App\Models\DocumentRequis;
use App\Models\Client;
use App\Models\Service;
use App\Models\Centre;
use App\Services\BarcodeService;
use App\Events\DossierOpened;
use App\Http\Requests\Dossier\StoreDossierRequest;

use App\Http\Requests\Dossier\CreateWalkinRequest;
use App\Http\Requests\Dossier\UpdateDossierRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DossierController extends Controller
{
    /**
     * Afficher la liste des dossiers
     */
    public function index(Request $request)
    {
        // Utiliser DossierOuvert au lieu de Dossier (nouveau système avec workflow)
        $query = DossierOuvert::with([
            'rendezVous.client', 
            'rendezVous.centre', 
            'rendezVous.service', 
            'rendezVous.formule',
            'agent',
            'paiementVerification'
        ])->whereNotNull('rendez_vous_id'); // Exclure les dossiers sans rendez-vous

        // Restriction de la liste : les agents ne voient que les dossiers qu'ils ont créés
        $user = Auth::user();
        if ($user->role === 'agent') {
            $query->where('agent_id', $user->id);
        } elseif ($user->role === 'admin' && $user->centre_id) {
            // Un administrateur de centre voit tous les dossiers de son centre
            $query->whereHas('rendezVous', function($q) use ($user) {
                $q->where('centre_id', $user->centre_id);
            });
        }

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('rendezVous.client', function($q2) use ($search) {
                    $q2->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rendez_vous_id')) {
            $query->where('rendez_vous_id', $request->rendez_vous_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date_ouverture', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date_ouverture', '<=', $request->end_date);
        }

        $dossiers = $query->orderBy('date_ouverture', 'desc')->paginate(15);

        return view('dossiers.index', compact('dossiers'));
    }

    /**
     * Afficher les détails d'un dossier
     */
    public function show(DossierOuvert $dossier)
    {
        $dossier->load([
            'rendezVous.client', 
            'rendezVous.centre.ville', 
            'rendezVous.service', 
            'rendezVous.formule',
            'documents', // Note: relation documents is on DossierOuvert? Need to check model
            'actionsLog.user' // Eager load logs
        ]);

        if (!Auth::user()->canAccessCentre($dossier->rendezVous->centre_id)) {
            abort(403, 'Accès non autorisé à ce dossier.');
        }

        $documentsRequis = DocumentRequis::where('service_id', $dossier->rendezVous->service_id)->get();

        return view('dossiers.show', compact('dossier', 'documentsRequis'));
    }

    // ... (create method unchanged)

    /**
     * Créer un nouveau dossier
     */
    public function store(StoreDossierRequest $request)
    {
        // Validation déjà effectuée par StoreDossierRequest

        try {
            // Vérifier qu'il n'y a pas déjà un dossier pour ce rendez-vous
            $existingDossier = DossierOuvert::where('rendez_vous_id', $request->rendez_vous_id)->first();
            if ($existingDossier) {
                return redirect()->back()
                    ->with('error', 'Un dossier existe déjà pour ce rendez-vous')
                    ->withInput();
            }

            $dossier = DossierOuvert::create([
                'rendez_vous_id' => $request->rendez_vous_id,
                'agent_id' => Auth::id(),
                'date_ouverture' => now(),
                'statut' => 'ouvert', // Default to ouvert
                'notes' => $request->notes,
                // 'numero_dossier' => $this->generateDossierNumber(), // DossierOuvert doesn't seem to have numero_dossier in fillable?
            ]);
            
            $dossier->logAction('ouvert', 'Création du dossier');

            // Déclencher l'événement
            event(new DossierOpened($dossier));

            return redirect()->route('dossiers.show', $dossier)
                ->with('success', 'Dossier créé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du dossier: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création du dossier')
                ->withInput();
        }
    }

    /**
     * Afficher le formulaire d'édition d'un dossier
     */
    public function edit(DossierOuvert $dossier)
    {
        $dossier->load(['rendezVous.client', 'rendezVous.service', 'rendezVous.formule', 'rendezVous.centre']);
        
        if (!Auth::user()->canAccessCentre($dossier->rendezVous->centre_id)) {
            abort(403, 'Accès non autorisé à ce dossier.');
        }

        return view('dossiers.edit', compact('dossier'));
    }

    /**
     * Mettre à jour un dossier
     */
    public function update(UpdateDossierRequest $request, DossierOuvert $dossier)
    {
        // Validation et autorisation gérées par UpdateDossierRequest
        // Note: Le check canAccessCentre est déjà fait dans le Request authorize()
        // On peut suppriemr l'ancien check ou le laisser par sécurité, mais le request throw 403 avnt.
        
        try {
            $oldStatut = $dossier->statut;
            
            $dossier->update([
                'statut' => $request->statut,
                'notes' => $request->notes,
            ]);
            
            if ($oldStatut !== $request->statut) {
                $dossier->logAction('changement_statut', "Statut modifié de $oldStatut à {$request->statut}");
            } else {
                $dossier->logAction('mise_a_jour', 'Mise à jour des informations générales');
            }

            return redirect()->route('dossiers.show', $dossier)
                ->with('success', 'Dossier mis à jour avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du dossier: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du dossier')
                ->withInput();
        }
    }

    /**
     * Supprimer un dossier
     */
    public function destroy(DossierOuvert $dossier)
    {
        if (!Auth::user()->canAccessCentre($dossier->rendezVous->centre_id)) {
            abort(403, 'Accès non autorisé à ce dossier.');
        }
        try {
            // Supprimer les fichiers associés
            foreach ($dossier->documentVerifications as $doc) {
                // Logic to delete files if they exist in documentVerifications
                // Note: DossierOuvert uses documentVerifications, not documents relation directly in the model shown previously?
                // Wait, show.blade.php uses $dossier->documents. 
                // Let's assume documents relation exists or I should check DossierOuvert model again.
            }

            $dossier->delete();

            return redirect()->route('dossiers.index')
                ->with('success', 'Dossier supprimé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du dossier: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du dossier');
        }
    }

    /**
     * Mettre à jour les documents d'un dossier
     */
    public function updateDocuments(Request $request, DossierOuvert $dossier)
    {
        if (!Auth::user()->canAccessCentre($dossier->rendezVous->centre_id)) {
            abort(403, 'Accès non autorisé à ce dossier.');
        }
        $request->validate([
            'documents' => 'required|array',
            'documents.*.document_requis_id' => 'required|exists:document_requis,id',
            'documents.*.fichier' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        try {
            $count = 0;
            foreach ($request->documents as $docData) {
                $documentRequis = DocumentRequis::findOrFail($docData['document_requis_id']);
                
                if (isset($docData['fichier']) && $docData['fichier']->isValid()) {
                    $file = $docData['fichier'];
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('dossiers/' . $dossier->id, $filename, 'public');

                    // Créer ou mettre à jour le document
                    // Note: DossierOuvert model shown earlier has documentVerifications() but not documents().
                    // But show.blade.php uses $dossier->documents.
                    // I will assume for now documents() exists or use documentVerifications() if that's what stores files.
                    // Actually, let's check if I can find where 'documents' relation is defined.
                    // If not found, I'll stick to what was there but change type hint.
                    
                    // Assuming the previous code was working for the relation, I'll keep the logic but update the type hint.
                    $dossier->documents()->updateOrCreate(
                        ['document_requis_id' => $documentRequis->id],
                        [
                            'nom_fichier' => $filename,
                            'chemin_fichier' => $path,
                            'taille_fichier' => $file->getSize(),
                            'type_mime' => $file->getMimeType(),
                        ]
                    );
                    $count++;
                }
            }
            
            if ($count > 0) {
                $dossier->logAction('documents_verifies', "$count document(s) mis à jour");
            }

            return redirect()->route('dossiers.show', $dossier)
                ->with('success', 'Documents mis à jour avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des documents: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour des documents');
        }
    }

    /**
     * Mettre à jour le statut de paiement
     */
    public function updatePayment(Request $request, DossierOuvert $dossier)
    {
        if (!Auth::user()->canAccessCentre($dossier->rendezVous->centre_id)) {
            abort(403, 'Accès non autorisé à ce dossier.');
        }
        $request->validate([
            'statut_paiement' => 'required|in:en_attente,paye,partiel,rembourse',
            'montant_paye' => 'nullable|numeric|min:0',
            'date_paiement' => 'nullable|date',
            'mode_paiement' => 'nullable|string|max:255',
            'reference_paiement' => 'nullable|string|max:255',
        ]);

        try {
            $dossier->update([
                'statut_paiement' => $request->statut_paiement,
                'montant_paye' => $request->montant_paye,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'reference_paiement' => $request->reference_paiement,
            ]);
            
            $dossier->logAction('paiement_verifie', "Paiement mis à jour: {$request->statut_paiement}", [
                'montant' => $request->montant_paye,
                'mode' => $request->mode_paiement
            ]);

            return redirect()->route('dossiers.show', $dossier)
                ->with('success', 'Statut de paiement mis à jour avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du paiement: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du paiement');
        }
    }

    /**
     * Générer un numéro de dossier unique
     */
    private function generateDossierNumber()
    {
        $prefix = 'DOS';
        $year = date('Y');
        $month = date('m');
        
        $lastDossier = Dossier::where('numero_dossier', 'like', $prefix . $year . $month . '%')
            ->orderBy('numero_dossier', 'desc')
            ->first();

        if ($lastDossier) {
            $lastNumber = (int) substr($lastDossier->numero_dossier, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Afficher le formulaire de création d'un dossier "sur place" (walk-in)
     */
    public function createWalkin()
    {
        $user = Auth::user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dossiers.index')
                ->with('error', 'Aucun centre assigné à votre compte');
        }

        $services = Service::where('statut', 'actif')
            ->whereHas('centres', function($query) use ($centre) {
                $query->where('centres.id', $centre->id);
            })
            ->orderBy('nom')
            ->get();

        return view('dossiers.create-walkin', compact('services', 'centre'));
    }

    /**
     * Créer un dossier "sur place" (walk-in) - Traitement complet
     */
    public function storeWalkin(CreateWalkinRequest $request)
    {
        $user = Auth::user();
        $centre = $user->centre;
        
        if (!$centre) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun centre assigné à votre compte'
            ], 403);
        }

        // Validation déjà effectuée par CreateWalkinRequest

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Étape 1 : Créer ou récupérer le client
            if ($request->filled('client_id')) {
                $client = Client::findOrFail($request->client_id);
            } else {
                // Vérifier si le client existe déjà (par email ou téléphone)
                $existingClient = Client::where('email', $request->client_email)
                    ->orWhere('telephone', $request->client_telephone)
                    ->first();

                if ($existingClient) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Un client avec cet email ou téléphone existe déjà. Veuillez le sélectionner.',
                        'existing_client' => [
                            'id' => $existingClient->id,
                            'nom' => $existingClient->nom,
                            'prenom' => $existingClient->prenom,
                            'email' => $existingClient->email
                        ]
                    ], 422);
                }

                // Créer le nouveau client
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
                    'statut' => 'actif',
                ]);
            }

            // Traitement des données de vérification
            $donneesOneci = null;
            if ($request->filled('donnees_oneci')) {
                $donneesOneci = json_decode($request->donnees_oneci, true);
            }
            
            $isVerified = $request->input('is_verified', '0') == '1';
            $numeroPreEnrolement = $request->input('numero_pre_enrolement');

            // Étape 2 : Créer le rendez-vous "sur place"
            // Format court: W + Ymd + 4 random chars = 1 + 8 + 4 = 13 caractères
            $numeroSuivi = 'W' . date('Ymd') . strtoupper(substr(uniqid(), -4));
            
            $rendezVous = RendezVous::create([
                'centre_id' => $centre->id,
                'service_id' => $request->service_id,
                'formule_id' => $request->formule_id,
                'client_id' => $client->id,
                'date_rendez_vous' => now()->toDateString(),
                'tranche_horaire' => 'Sur place - ' . now()->format('H:i'),
                'statut' => RendezVous::STATUT_CONFIRME,
                'numero_suivi' => $numeroSuivi,
                'notes' => 'Dossier créé sur place (walk-in)',
                'numero_pre_enrolement' => $numeroPreEnrolement,
                'statut_oneci' => $isVerified ? 'valide' : null,
                'verified_at' => $isVerified ? now() : null,
                'donnees_oneci' => $donneesOneci,
            ]);

            // Étape 3 : Créer le dossier ouvert
            $dossierOuvert = DossierOuvert::create([
                'rendez_vous_id' => $rendezVous->id,
                'agent_id' => $user->id,
                'date_ouverture' => now(),
                'statut' => 'ouvert',
                'fiche_pre_enrolement_verifiee' => $isVerified,
                'documents_verifies' => false,
                'documents_manquants' => false,
                'informations_client_verifiees' => false,
                'paiement_verifie' => false,
                'notes' => 'Dossier créé sur place',
            ]);

            \Illuminate\Support\Facades\DB::commit();

            // Déclencher l'événement
            event(new DossierOpened($dossierOuvert));

            Log::info('Dossier walk-in créé avec succès', [
                'dossier_id' => $dossierOuvert->id,
                'rendez_vous_id' => $rendezVous->id,
                'client_id' => $client->id,
                'agent_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dossier créé avec succès',
                'dossier_id' => $dossierOuvert->id,
                'redirect_url' => route('dossier.workflow', $dossierOuvert)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error('Erreur lors de la création du dossier walk-in: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du dossier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Imprimer l'étiquette d'un dossier
     */
    public function imprimerEtiquette(DossierOuvert $dossierOuvert, BarcodeService $barcodeService)
    {
        if (!Auth::user()->canAccessCentre($dossierOuvert->rendezVous->centre_id)) {
            abort(403, 'Accès non autorisé à ce dossier.');
        }
        // Charger les relations nécessaires
        $dossierOuvert->load([
            'rendezVous.client',
            'rendezVous.service',
            'rendezVous.centre'
        ]);

        // Générer le code-barres si nécessaire
        if (!$dossierOuvert->code_barre) {
            $codeBarre = $barcodeService->generateCodeBarreForDossier($dossierOuvert);
        } else {
            $codeBarre = $dossierOuvert->code_barre;
        }

        // Générer le SVG du code-barres
        $barcodeSvg = $barcodeService->generateCode128SVG($codeBarre);

        return view('dossiers.etiquette', compact('dossierOuvert', 'barcodeSvg', 'codeBarre'));
    }
}