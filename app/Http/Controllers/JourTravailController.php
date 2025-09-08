<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JourTravail;
use App\Models\Centre;

class JourTravailController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }
        
        $joursTravail = JourTravail::where('centre_id', $centre->id)
            ->orderBy('jour_semaine')
            ->get();
            
        return view('jours-travail.index', compact('joursTravail', 'centre'));
    }

    public function create()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }
        
        return view('jours-travail.create', compact('centre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jour_semaine' => 'required|integer|between:1,7',
            'actif' => 'required|boolean',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'pause_debut' => 'nullable|date_format:H:i',
            'pause_fin' => 'nullable|date_format:H:i|after:pause_debut',
        ]);

        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        // Vérifier si le jour existe déjà
        $existingJour = JourTravail::where('centre_id', $centre->id)
            ->where('jour_semaine', $request->jour_semaine)
            ->first();

        if ($existingJour) {
            return back()->withErrors(['jour_semaine' => 'Ce jour de la semaine est déjà configuré.'])->withInput();
        }

        JourTravail::create([
            'centre_id' => $centre->id,
            'jour_semaine' => $request->jour_semaine,
            'actif' => $request->actif,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'pause_debut' => $request->pause_debut,
            'pause_fin' => $request->pause_fin,
        ]);

        return redirect()->route('jours-travail.index')->with('success', 'Jour de travail configuré avec succès.');
    }

    public function edit(JourTravail $jourTravail)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $jourTravail->centre_id !== $centre->id) {
            return redirect()->route('jours-travail.index')->with('error', 'Jour de travail non trouvé.');
        }
        
        return view('jours-travail.edit', compact('jourTravail'));
    }

    public function update(Request $request, JourTravail $jourTravail)
    {
        $request->validate([
            'actif' => 'required|boolean',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'pause_debut' => 'nullable|date_format:H:i',
            'pause_fin' => 'nullable|date_format:H:i|after:pause_debut',
        ]);

        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $jourTravail->centre_id !== $centre->id) {
            return redirect()->route('jours-travail.index')->with('error', 'Jour de travail non trouvé.');
        }

        $jourTravail->update($request->only(['actif', 'heure_debut', 'heure_fin', 'pause_debut', 'pause_fin']));

        return redirect()->route('jours-travail.index')->with('success', 'Jour de travail mis à jour avec succès.');
    }

    public function destroy(JourTravail $jourTravail)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $jourTravail->centre_id !== $centre->id) {
            return redirect()->route('jours-travail.index')->with('error', 'Jour de travail non trouvé.');
        }

        $jourTravail->delete();

        return redirect()->route('jours-travail.index')->with('success', 'Jour de travail supprimé avec succès.');
    }

    public function toggle(JourTravail $jourTravail)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $jourTravail->centre_id !== $centre->id) {
            return redirect()->route('jours-travail.index')->with('error', 'Jour de travail non trouvé.');
        }

        $jourTravail->update(['actif' => !$jourTravail->actif]);

        return redirect()->route('jours-travail.index')->with('success', 'Statut du jour de travail mis à jour.');
    }
}