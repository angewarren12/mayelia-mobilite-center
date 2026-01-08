<?php

namespace App\Http\Controllers;

use App\Models\RetraitCarte;
use App\Models\Ticket;
use App\Models\Service;
use App\Models\Client;
use App\Models\CentreCarteStock;
use App\Models\CarteReception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\AuthService;

class RetraitCarteController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    private function checkAccess()
    {
        $user = Auth::user();
        if (!$user || !($this->authService->isAdmin() || in_array($user->role, ['agent', 'agent_biometrie']))) {
            abort(403, 'Accès non autorisé au module de retrait.');
        }
    }

    /**
     * Liste des retraits
     */
    public function index(Request $request)
    {
        $this->checkAccess();
        $user = Auth::user();
        $centreId = $user->centre_id;

        if (!$centreId) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre associé à votre compte.');
        }

        $query = RetraitCarte::where('centre_id', $centreId)
            ->with(['client', 'ticket', 'agent']);

        // Filtre Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_recepisse', 'like', "%{$search}%")
                  ->orWhere('numero_piece_finale', 'like', "%{$search}%")
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre Statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut === 'terminé' ? 'termine' : 'en_cours');
        }

        // Filtre Date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $retraits = $query->orderBy('created_at', 'desc')->paginate(15);

        // Récupérer le stock actuel pour affichage rapide
        $stocks = CentreCarteStock::where('centre_id', $centreId)->get()->keyBy('type_piece');

        return view('retraits.index', compact('retraits', 'stocks'));
    }

    /**
     * Gestion du stock (Réception de cartes)
     */
    public function stock()
    {
        $this->checkAccess();
        $user = Auth::user();
        $centreId = $user->centre_id;

        $stocks = CentreCarteStock::where('centre_id', $centreId)->get();
        $receptions = CarteReception::where('centre_id', $centreId)
            ->with('createur')
            ->orderBy('date_reception', 'desc')
            ->paginate(15);

        return view('retraits.stock', compact('stocks', 'receptions'));
    }

    /**
     * Enregistrer une réception de cartes
     */
    public function storeStock(Request $request)
    {
        $this->checkAccess();
        $user = Auth::user();
        
        $request->validate([
            'type_piece' => 'required|in:CNI,Résident',
            'quantite' => 'required|integer|min:1',
            'date_reception' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // 1. Créer la ligne de réception
            CarteReception::create([
                'centre_id' => $user->centre_id,
                'type_piece' => $request->type_piece,
                'quantite' => $request->quantite,
                'date_reception' => $request->date_reception,
                'created_by' => $user->id,
                'notes' => $request->notes
            ]);

            // 2. Mettre à jour le stock
            $stock = CentreCarteStock::firstOrCreate(
                ['centre_id' => $user->centre_id, 'type_piece' => $request->type_piece],
                ['quantite' => 0]
            );
            $stock->increment('quantite', $request->quantite);

            DB::commit();

            return back()->with('success', 'Réception enregistrée et stock mis à jour.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur reception stock: ' . $e->getMessage());
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $this->checkAccess();
        $ticket = null;
        if ($request->has('ticket_id')) {
            $ticket = Ticket::with('client')->find($request->ticket_id);
        }
        return view('retraits.create', compact('ticket'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();
        $user = Auth::user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:255',
            'type_piece' => 'required|in:CNI,Résident',
            'numero_recepisse' => 'required|string|max:255',
            'scan_recepisse' => 'nullable|image|max:5120',
            'ticket_id' => 'nullable|exists:tickets,id'
        ]);

        try {
            DB::beginTransaction();

            $client = Client::updateOrCreate(
                ['telephone' => $request->telephone],
                ['nom' => $request->nom, 'prenom' => $request->prenom, 'statut' => 'actif']
            );

            $retrait = RetraitCarte::create([
                'ticket_id' => $request->ticket_id,
                'centre_id' => $user->centre_id,
                'client_id' => $client->id,
                'type_piece' => $request->type_piece,
                'numero_recepisse' => $request->numero_recepisse,
                'statut' => 'en_cours',
                'user_id' => $user->id
            ]);

            if ($request->hasFile('scan_recepisse')) {
                $path = $request->file('scan_recepisse')->store('retraits/' . $retrait->id, 'public');
                $retrait->update(['scan_recepisse' => $path]);
            }

            // Si lié à un ticket, on marque le ticket comme en cours
            if ($request->ticket_id) {
                Ticket::where('id', $request->ticket_id)->update(['statut' => Ticket::STATUT_EN_COURS]);
            }

            DB::commit();

            return redirect()->route('retraits.index')->with('success', 'Retrait enregistré.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur retrait store: ' . $e->getMessage());
            return back()->with('error', 'Erreur : ' . $e->getMessage())->withInput();
        }
    }

    public function finaliser(Request $request, RetraitCarte $retrait)
    {
        $this->checkAccess();
        $request->validate([
            'numero_piece' => 'required|string|max:255',
            'date_expiration' => 'required|date',
            'telephone' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier le stock
            $stock = CentreCarteStock::where('centre_id', $retrait->centre_id)
                ->where('type_piece', $retrait->type_piece)
                ->first();

            if (!$stock || $stock->quantite <= 0) {
                throw new \Exception('Stock insuffisant pour ce type de pièce dans ce centre.');
            }

            // Décrémenter le stock
            $stock->decrement('quantite', 1);

            // Mettre à jour le retrait
            $retrait->update([
                'numero_piece_finale' => $request->numero_piece,
                'date_expiration_piece' => $request->date_expiration,
                'statut' => 'termine',
            ]);

            // Mettre à jour le client
            $retrait->client->update([
                'telephone' => $request->telephone,
                'numero_piece_identite' => $request->numero_piece,
                'type_piece_identite' => $retrait->type_piece
            ]);

            // Si lié à un ticket, finaliser le ticket
            if ($retrait->ticket_id) {
                Ticket::where('id', $retrait->ticket_id)->update([
                    'statut' => Ticket::STATUT_TERMINÉ,
                    'completed_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('retraits.index')->with('success', 'La carte a été remise. Stock mis à jour (-1).');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur retrait finaliser: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(RetraitCarte $retrait)
    {
        $this->checkAccess();
        if (!$this->authService->isAdmin()) {
            abort(403);
        }

        if ($retrait->scan_recepisse) {
            Storage::disk('public')->delete($retrait->scan_recepisse);
        }
        $retrait->delete();

        return back()->with('success', 'Retrait supprimé.');
    }

    public function traitement(RetraitCarte $retrait)
    {
        $this->checkAccess();
        return view('retraits.traitement', compact('retrait'));
    }

    public function storeInfo(Request $request, RetraitCarte $retrait)
    {
        $this->checkAccess();
        $request->validate([
            'type_piece' => 'required|in:CNI,Résident',
            'numero_recepisse' => 'required|string|max:255',
            'scan_recepisse' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['type_piece', 'numero_recepisse']);
        if ($request->hasFile('scan_recepisse')) {
            if ($retrait->scan_recepisse) {
                Storage::disk('public')->delete($retrait->scan_recepisse);
            }
            $data['scan_recepisse'] = $request->file('scan_recepisse')->store('retraits/' . $retrait->id, 'public');
        }

        $retrait->update($data);
        return back()->with('success', 'Modifié.');
    }

    public function exportPdf(Request $request)
    {
        $this->checkAccess();
        $user = Auth::user();
        $centreId = $user->centre_id;

        $query = RetraitCarte::where('centre_id', $centreId)
            ->with(['client', 'ticket']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_recepisse', 'like', "%{$search}%")
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut === 'terminé' ? 'termine' : 'en_cours');
        }

        $retraits = $query->orderBy('created_at', 'asc')->get();
        
        $data = [
            'retraits' => $retraits,
            'centre' => $user->centre,
            'date_export' => now()->format('d/m/Y H:i'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('retraits.export-pdf', $data);
        return $pdf->download('retraits_carte_' . now()->format('Ymd_His') . '.pdf');
    }
}
