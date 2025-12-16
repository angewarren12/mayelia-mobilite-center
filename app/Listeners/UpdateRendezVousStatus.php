<?php

namespace App\Listeners;

use App\Events\DossierOpened;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Log;

class UpdateRendezVousStatus
{
    /**
     * Handle the event.
     */
    public function handle(DossierOpened $event): void
    {
        $dossierOuvert = $event->dossierOuvert;
        
        // Mettre à jour le statut du rendez-vous associé
        if ($dossierOuvert->rendez_vous_id) {
            $rendezVous = RendezVous::find($dossierOuvert->rendez_vous_id);
            if ($rendezVous && $rendezVous->statut === RendezVous::STATUT_CONFIRME) {
                $rendezVous->update(['statut' => RendezVous::STATUT_DOSSIER_OUVERT]);
                
                Log::info('Statut RDV mis à jour après ouverture de dossier', [
                    'rendez_vous_id' => $rendezVous->id,
                    'nouveau_statut' => RendezVous::STATUT_DOSSIER_OUVERT,
                ]);
            }
        }
    }
}

