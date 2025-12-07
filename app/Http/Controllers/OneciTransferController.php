<?php

namespace App\Http\Controllers;

use App\Models\DossierOneciTransfer;
use App\Models\DossierOneciItem;
use App\Models\DossierOuvert;
use App\Services\BarcodeService;
use App\Services\NotificationService;
use App\Services\OneciEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Concerns\ChecksPermissions;

class OneciTransferController extends Controller
{
    use ChecksPermissions;

    protected $barcodeService;
    protected $notificationService;
    protected $emailService;

    public function __construct(BarcodeService $barcodeService, NotificationService $notificationService, OneciEmailService $emailService)
    {
        $this->barcodeService = $barcodeService;
        $this->notificationService = $notificationService;
        $this->emailService = $emailService;
    }

    /**
     * Liste des transferts
     */
    public function index(Request $request)
    {
        $this->checkPermission('oneci-transfers', 'view');

        $user = Auth::user();
        $query = DossierOneciTransfer::with(['centre', 'agentMayelia', 'items']);

        // Filtrer par centre si agent
        if ($user->role === 'agent' && $user->centre_id) {
            $query->where('centre_id', $user->centre_id);
        }

        // Filtres
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date_envoi', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date_envoi', '<=', $request->date_to);
        }

        $transfers = $query->orderBy('date_envoi', 'desc')->paginate(15);

        return view('oneci-transfers.index', compact('transfers'));
    }

    /**
     * Afficher les dossiers finalisés disponibles pour envoi
     */
    public function create(Request $request)
    {
        $this->checkPermission('oneci-transfers', 'create');

        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        // Récupérer les dossiers finalisés non encore envoyés
        $query = DossierOuvert::where('statut', 'finalise')
            ->whereNull('transfer_id')
            ->whereHas('rendezVous', function($q) use ($centre) {
                $q->where('centre_id', $centre->id);
            })
            ->with(['rendezVous.client', 'rendezVous.service', 'agent']);

        // Filtre par date
        if ($request->has('date_finalisation')) {
            $query->whereDate('updated_at', $request->date_finalisation);
        } else {
            // Par défaut, dossiers finalisés aujourd'hui
            $query->whereDate('updated_at', today());
        }

        $dossiers = $query->orderBy('updated_at', 'desc')->get();

        return view('oneci-transfers.create', compact('dossiers', 'centre'));
    }

    /**
     * Créer un transfert et générer les codes-barres
     */
    public function store(Request $request)
    {
        $this->checkPermission('oneci-transfers', 'create');

        $request->validate([
            'dossiers' => 'required|array|min:1',
            'dossiers.*' => 'exists:dossier_ouvert,id',
            'date_envoi' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $centre = $user->centre;

        if (!$centre) {
            return back()->with('error', 'Aucun centre assigné.');
        }

        DB::beginTransaction();
        try {
            // Créer le transfert
            $transfer = DossierOneciTransfer::create([
                'centre_id' => $centre->id,
                'date_envoi' => $request->date_envoi,
                'statut' => 'en_attente',
                'code_transfert' => DossierOneciTransfer::generateCodeTransfert(),
                'agent_mayelia_id' => $user->id,
                'notes' => $request->notes,
            ]);

            // Traiter chaque dossier
            $dossiers = DossierOuvert::whereIn('id', $request->dossiers)
                ->where('statut', 'finalise')
                ->whereNull('transfer_id')
                ->get();

            foreach ($dossiers as $dossier) {
                // Générer le code-barres
                $codeBarre = $this->barcodeService->generateCodeBarreForDossier($dossier);

                // Créer l'item ONECI
                $item = DossierOneciItem::create([
                    'transfer_id' => $transfer->id,
                    'dossier_ouvert_id' => $dossier->id,
                    'code_barre' => $codeBarre,
                    'statut' => 'en_attente',
                ]);

                // Mettre à jour le dossier
                $dossier->update([
                    'transfer_id' => $transfer->id,
                    'statut_oneci' => 'envoye',
                    'date_envoi_oneci' => now(),
                    'code_barre' => $codeBarre,
                ]);
            }

            // Mettre à jour le nombre de dossiers
            $transfer->update(['nombre_dossiers' => $dossiers->count()]);

            DB::commit();

            return redirect()
                ->route('oneci-transfers.show', $transfer)
                ->with('success', "Transfert créé avec succès. {$dossiers->count()} dossier(s) inclus.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création du transfert: ' . $e->getMessage());
        }
    }

    /**
     * Détails d'un transfert
     */
    public function show(DossierOneciTransfer $oneciTransfer)
    {
        $this->checkPermission('oneci-transfers', 'view');

        $transfer = $oneciTransfer->load([
            'centre',
            'agentMayelia',
            'agentOneci',
            'items.dossierOuvert.rendezVous.client',
            'items.dossierOuvert.rendezVous.service'
        ]);

        return view('oneci-transfers.show', compact('transfer'));
    }

    /**
     * Marquer le transfert comme envoyé et envoyer l'email à ONECI
     */
    public function envoyer(DossierOneciTransfer $oneciTransfer)
    {
        $this->checkPermission('oneci-transfers', 'envoyer');

        if ($oneciTransfer->statut !== 'en_attente') {
            return back()->with('error', 'Ce transfert a déjà été envoyé ou a un autre statut.');
        }

        DB::beginTransaction();
        try {
            // Mettre à jour le statut
            $oneciTransfer->update([
                'statut' => 'envoye',
            ]);

            // Envoyer l'email avec le PDF à ONECI
            $emailSent = $this->emailService->sendTransferEmail($oneciTransfer);

            DB::commit();

            if ($emailSent) {
                return back()->with('success', 'Transfert marqué comme envoyé. Email avec PDF envoyé à l\'ONECI.');
            } else {
                return back()->with('warning', 'Transfert marqué comme envoyé, mais l\'email n\'a pas pu être envoyé. Vérifiez la configuration email.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de l\'envoi du transfert: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'envoi du transfert: ' . $e->getMessage());
        }
    }

    /**
     * Imprimer les étiquettes avec codes-barres
     */
    public function imprimerEtiquettes(DossierOneciTransfer $oneciTransfer)
    {
        $this->checkPermission('oneci-transfers', 'view');

        $transfer = $oneciTransfer->load([
            'items.dossierOuvert.rendezVous.client',
            'items.dossierOuvert.rendezVous.service'
        ]);

        // Générer les codes-barres SVG pour chaque item
        $items = $transfer->items->map(function($item) {
            $item->barcode_svg = $this->barcodeService->generateCode128SVG($item->code_barre);
            return $item;
        });

        return view('oneci-transfers.etiquettes', compact('transfer', 'items'));
    }
}
