<?php

namespace App\Http\Controllers;

use App\Models\Centre;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CentreQmsSettingsController extends Controller
{
    /**
     * Afficher le formulaire de configuration QMS
     */
    public function edit(Centre $centre, AuthService $authService)
    {
        // Vérifier que l'utilisateur est admin
        if (!$authService->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        return view('admin.centres.qms-settings', compact('centre'));
    }

    /**
     * Mettre à jour les paramètres QMS du centre
     */
    public function update(Request $request, Centre $centre, AuthService $authService)
    {
        // Vérifier que l'utilisateur est admin
        if (!$authService->isAdmin()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'qms_mode' => 'required|in:fifo,fenetre_tolerance',
            'qms_fenetre_minutes' => 'nullable|integer|min:5|max:60'
        ]);

        $centre->update([
            'qms_mode' => $request->qms_mode,
            'qms_fenetre_minutes' => $request->qms_fenetre_minutes ?? 15
        ]);

        // Invalider le cache des infos du centre
        Cache::forget("centre_info_{$centre->id}");

        return redirect()->back()->with('success', 'Paramètres QMS mis à jour avec succès');
    }
}
