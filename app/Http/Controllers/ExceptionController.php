<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exception;
use App\Models\Centre;

class ExceptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }
        
        $exceptions = Exception::where('centre_id', $centre->id)
            ->orderBy('date_exception', 'desc')
            ->get();
            
        return view('exceptions.index', compact('exceptions', 'centre'));
    }

    public function create()
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }
        
        return view('exceptions.create', compact('centre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_exception' => 'required|date|after:today',
            'type' => 'required|in:ferme,capacite_reduite,horaires_modifies',
            'description' => 'nullable|string',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
            'pause_debut' => 'nullable|date_format:H:i',
            'pause_fin' => 'nullable|date_format:H:i|after:pause_debut',
            'capacite_reduite' => 'nullable|integer|min:1|max:20',
        ]);

        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre) {
            return redirect()->route('dashboard')->with('error', 'Aucun centre assigné.');
        }

        Exception::create([
            'centre_id' => $centre->id,
            'date_exception' => $request->date_exception,
            'type' => $request->type,
            'description' => $request->description,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'pause_debut' => $request->pause_debut,
            'pause_fin' => $request->pause_fin,
            'capacite_reduite' => $request->capacite_reduite,
        ]);

        return redirect()->route('exceptions.index')->with('success', 'Exception créée avec succès.');
    }

    public function edit(Exception $exception)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $exception->centre_id !== $centre->id) {
            return redirect()->route('exceptions.index')->with('error', 'Exception non trouvée.');
        }
        
        return view('exceptions.edit', compact('exception'));
    }

    public function update(Request $request, Exception $exception)
    {
        $request->validate([
            'type' => 'required|in:ferme,capacite_reduite,horaires_modifies',
            'description' => 'nullable|string',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
            'pause_debut' => 'nullable|date_format:H:i',
            'pause_fin' => 'nullable|date_format:H:i|after:pause_debut',
            'capacite_reduite' => 'nullable|integer|min:1|max:20',
        ]);

        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $exception->centre_id !== $centre->id) {
            return redirect()->route('exceptions.index')->with('error', 'Exception non trouvée.');
        }

        $exception->update($request->only([
            'type', 'description', 'heure_debut', 'heure_fin', 
            'pause_debut', 'pause_fin', 'capacite_reduite'
        ]));

        return redirect()->route('exceptions.index')->with('success', 'Exception mise à jour avec succès.');
    }

    public function destroy(Exception $exception)
    {
        $user = auth()->user();
        $centre = $user->centre;
        
        if (!$centre || $exception->centre_id !== $centre->id) {
            return redirect()->route('exceptions.index')->with('error', 'Exception non trouvée.');
        }

        $exception->delete();

        return redirect()->route('exceptions.index')->with('success', 'Exception supprimée avec succès.');
    }
}