<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemplateCreneau;
use App\Models\Service;
use App\Models\Formule;
use App\Models\JourTravail;
use App\Models\Centre;
use App\Services\CreneauGeneratorService;

class TemplateCreneauController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }
        
        $templates = TemplateCreneau::where('centre_id', $centre->id)
            ->with(['service', 'formule'])
            ->orderBy('jour_semaine')
            ->orderBy('tranche_horaire')
            ->get();
            
        $services = Service::where('centre_id', $centre->id)
            ->where('statut', 'actif')
            ->with('formules')
            ->get();
            
        $joursTravail = JourTravail::where('centre_id', $centre->id)
            ->where('actif', true)
            ->orderBy('jour_semaine')
            ->get();
            
        return view('templates.index', compact('templates', 'services', 'joursTravail', 'centre'));
    }

    public function create()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }
        
        $services = Service::where('centre_id', $centre->id)
            ->where('statut', 'actif')
            ->with('formules')
            ->get();
            
        $joursTravail = JourTravail::where('centre_id', $centre->id)
            ->where('actif', true)
            ->orderBy('jour_semaine')
            ->get();
            
        return view('templates.create', compact('services', 'joursTravail', 'centre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'formule_id' => 'required|exists:formules,id',
            'jour_semaine' => 'required|integer|between:1,7',
            'tranche_horaire' => 'required|string',
            'capacite' => 'required|integer|min:1|max:20',
        ]);

        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        // Vérifier que le service appartient au centre
        $service = Service::where('id', $request->service_id)
            ->where('centre_id', $centre->id)
            ->first();
            
        if (!$service) {
            return back()->withErrors(['service_id' => 'Service non trouvé.'])->withInput();
        }

        // Vérifier que la formule appartient au service
        $formule = Formule::where('id', $request->formule_id)
            ->where('service_id', $request->service_id)
            ->first();
            
        if (!$formule) {
            return back()->withErrors(['formule_id' => 'Formule non trouvée.'])->withInput();
        }

        // Vérifier que le jour de travail est actif
        $jourTravail = JourTravail::where('centre_id', $centre->id)
            ->where('jour_semaine', $request->jour_semaine)
            ->where('actif', true)
            ->first();
            
        if (!$jourTravail) {
            return back()->withErrors(['jour_semaine' => 'Ce jour n\'est pas configuré comme jour de travail.'])->withInput();
        }

        // Vérifier si le template existe déjà
        $existingTemplate = TemplateCreneau::where('centre_id', $centre->id)
            ->where('service_id', $request->service_id)
            ->where('formule_id', $request->formule_id)
            ->where('jour_semaine', $request->jour_semaine)
            ->where('tranche_horaire', $request->tranche_horaire)
            ->first();

        if ($existingTemplate) {
            return back()->withErrors(['tranche_horaire' => 'Ce template existe déjà pour cette combinaison.'])->withInput();
        }

        TemplateCreneau::create([
            'centre_id' => $centre->id,
            'service_id' => $request->service_id,
            'formule_id' => $request->formule_id,
            'jour_semaine' => $request->jour_semaine,
            'tranche_horaire' => $request->tranche_horaire,
            'capacite' => $request->capacite,
            'statut' => 'actif'
        ]);

        return redirect()->route('templates.index')->with('success', 'Template de créneau créé avec succès.');
    }

    public function edit(TemplateCreneau $template)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $template->centre_id !== $centre->id) {
            return redirect()->route('templates.index')->with('error', 'Template non trouvé.');
        }
        
        $services = Service::where('centre_id', $centre->id)
            ->where('statut', 'actif')
            ->with('formules')
            ->get();
            
        $joursTravail = JourTravail::where('centre_id', $centre->id)
            ->where('actif', true)
            ->orderBy('jour_semaine')
            ->get();
            
        return view('templates.edit', compact('template', 'services', 'joursTravail'));
    }

    public function update(Request $request, TemplateCreneau $template)
    {
        $request->validate([
            'capacite' => 'required|integer|min:1|max:20',
            'statut' => 'required|in:actif,inactif'
        ]);

        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $template->centre_id !== $centre->id) {
            return redirect()->route('templates.index')->with('error', 'Template non trouvé.');
        }

        $template->update($request->only(['capacite', 'statut']));

        return redirect()->route('templates.index')->with('success', 'Template mis à jour avec succès.');
    }

    public function destroy(TemplateCreneau $template)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $template->centre_id !== $centre->id) {
            return redirect()->route('templates.index')->with('error', 'Template non trouvé.');
        }

        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template supprimé avec succès.');
    }

    public function generateCreneaux()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        $generator = new CreneauGeneratorService();
        $generator->generateCreneauxForNext6Months($centre);

        return redirect()->route('templates.index')->with('success', 'Créneaux générés avec succès pour les 6 prochains mois.');
    }

    public function getFormules(Request $request)
    {
        $serviceId = $request->service_id;
        $formules = Formule::where('service_id', $serviceId)
            ->where('statut', 'actif')
            ->get();
            
        return response()->json($formules);
    }

    public function getTranchesHoraires(Request $request)
    {
        $jourSemaine = $request->jour_semaine;
        $user = auth()->user();
        $centre = $user->centre;
        
        $jourTravail = JourTravail::where('centre_id', $centre->id)
            ->where('jour_semaine', $jourSemaine)
            ->where('actif', true)
            ->first();
            
        if (!$jourTravail) {
            return response()->json([]);
        }

        $tranches = $this->generateTranchesHoraires($jourTravail);
        
        return response()->json($tranches);
    }

    private function generateTranchesHoraires($jourTravail)
    {
        $tranches = [];
        $heureDebut = \Carbon\Carbon::createFromFormat('H:i', $jourTravail->heure_debut);
        $heureFin = \Carbon\Carbon::createFromFormat('H:i', $jourTravail->heure_fin);
        
        $pauseDebut = $jourTravail->pause_debut ? \Carbon\Carbon::createFromFormat('H:i', $jourTravail->pause_debut) : null;
        $pauseFin = $jourTravail->pause_fin ? \Carbon\Carbon::createFromFormat('H:i', $jourTravail->pause_fin) : null;
        
        $current = $heureDebut->copy();
        
        while ($current->lt($heureFin)) {
            $next = $current->copy()->addHour();
            
            // Vérifier si la tranche chevauche avec la pause
            if ($pauseDebut && $pauseFin) {
                if ($current->lt($pauseFin) && $next->gt($pauseDebut)) {
                    $current = $pauseFin;
                    continue;
                }
            }
            
            if ($next->lte($heureFin)) {
                $tranches[] = [
                    'value' => $current->format('H:i') . '-' . $next->format('H:i'),
                    'label' => $current->format('H:i') . ' - ' . $next->format('H:i')
                ];
            }
            
            $current->addHour();
        }
        
        return $tranches;
    }
}