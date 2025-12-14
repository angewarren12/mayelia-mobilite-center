<?php

namespace App\Services;

use App\Models\Centre;
use App\Models\Ticket;
use Carbon\Carbon;

class QmsPriorityService
{
    /**
     * Calcule la priorité d'un ticket selon le mode QMS du centre
     */
    public function calculatePriority(Centre $centre, Ticket $ticket): int
    {
        switch ($centre->qms_mode) {
            case 'fifo':
                return $this->fifoMode($ticket);
            
            case 'fenetre_tolerance':
                return $this->fenetreToleranceMode($centre, $ticket);
            
            default:
                return 1; // Priorité normale par défaut
        }
    }

    /**
     * Mode FIFO : Tous les tickets ont la même priorité
     * Premier arrivé, premier servi
     */
    private function fifoMode(Ticket $ticket): int
    {
        return 1; // Tous égaux
    }

    /**
     * Mode Fenêtre de Tolérance : 
     * Les RDV sont prioritaires uniquement dans leur fenêtre horaire
     */
    private function fenetreToleranceMode(Centre $centre, Ticket $ticket): int
    {
        // Si ce n'est pas un RDV ou pas d'heure définie
        if ($ticket->type !== 'rdv' || !$ticket->heure_rdv) {
            return 1; // Priorité normale
        }

        $heureRdv = Carbon::parse($ticket->heure_rdv);
        $maintenant = Carbon::now();
        $fenetreMinutes = $centre->qms_fenetre_minutes ?? 15;

        // Calculer la fenêtre de tolérance
        $debutFenetre = $heureRdv->copy()->subMinutes($fenetreMinutes);
        $finFenetre = $heureRdv->copy()->addMinutes($fenetreMinutes);

        // Si on est dans la fenêtre, priorité haute
        if ($maintenant->between($debutFenetre, $finFenetre)) {
            return 2; // Priorité haute
        }

        // Hors fenêtre = priorité normale
        return 1;
    }

    /**
     * Recalcule les priorités de tous les tickets en attente d'un centre
     */
    public function updateAllPriorities(Centre $centre): void
    {
        $tickets = Ticket::where('centre_id', $centre->id)
            ->where('statut', 'en_attente')
            ->get();

        foreach ($tickets as $ticket) {
            $priorite = $this->calculatePriority($centre, $ticket);
            $ticket->update(['priorite' => $priorite]);
        }
    }
}
