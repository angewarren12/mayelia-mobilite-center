<?php

namespace App\Observers;

use App\Models\DossierOuvert;

class DossierOuvertObserver
{
    /**
     * Handle the DossierOuvert "created" event.
     */
    public function created(DossierOuvert $dossierOuvert): void
    {
        $this->syncRendezVousStatus($dossierOuvert);
    }

    /**
     * Handle the DossierOuvert "updated" event.
     */
    public function updated(DossierOuvert $dossierOuvert): void
    {
        if ($dossierOuvert->isDirty('statut')) {
            $this->syncRendezVousStatus($dossierOuvert);
        }
    }

    /**
     * Handle the DossierOuvert "deleted" event.
     */
    public function deleted(DossierOuvert $dossierOuvert): void
    {
        // Optionnel : remettre le RDV en "confirme" si le dossier est supprimé ?
        // Pour l'instant on ne fait rien pour éviter des effets de bord indésirables.
    }

    /**
     * Synchronize the related RendezVous status based on DossierOuvert status.
     */
    protected function syncRendezVousStatus(DossierOuvert $dossierOuvert): void
    {
        if (!$dossierOuvert->rendez_vous_id) {
            return;
        }

        // Charger la relation si elle n'est pas chargée
        if (!$dossierOuvert->relationLoaded('rendezVous')) {
            $dossierOuvert->load('rendezVous');
        }

        $rendezVous = $dossierOuvert->rendezVous;
        
        if (!$rendezVous) {
            return;
        }

        $newStatus = null;

        switch ($dossierOuvert->statut) {
            case 'annulé':
                $newStatus = 'annule';
                break;
            
            case 'finalise':
                $newStatus = 'finalise';
                break;
            
            case 'ouvert':
            case 'en_cours':
                if ($dossierOuvert->paiement_verifie) {
                    $newStatus = 'paiement_effectue';
                } else {
                    $newStatus = 'dossier_ouvert';
                }
                break;
        }

        if ($newStatus && $rendezVous->statut !== $newStatus) {
            $rendezVous->update(['statut' => $newStatus]);
        }
    }
}
