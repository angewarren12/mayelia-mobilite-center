<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Centre;
use App\Models\DossierOneciItem;

class NotificationService
{
    /**
     * Notifier un utilisateur
     */
    public function notifyUser(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Notifier tous les agents d'un centre
     */
    public function notifyCentre(Centre $centre, string $type, string $title, string $message, array $data = []): int
    {
        $users = $centre->users()->where('role', 'agent')->get();
        $count = 0;

        foreach ($users as $user) {
            $this->notifyUser($user, $type, $title, $message, $data);
            $count++;
        }

        return $count;
    }

    /**
     * Notifier l'agent Mayelia quand une carte est prête
     */
    public function notifyAgentMayelia(DossierOneciItem $item): Notification
    {
        $dossier = $item->dossierOuvert;
        $rendezVous = $dossier->rendezVous;
        $client = $rendezVous->client;
        $centre = $rendezVous->centre;

        $title = 'Carte prête pour récupération';
        $message = "La carte du client {$client->nom_complet} (Dossier #{$dossier->id}) est prête à l'ONECI et peut être récupérée.";

        $data = [
            'dossier_ouvert_id' => $dossier->id,
            'item_id' => $item->id,
            'transfer_id' => $item->transfer_id,
            'client_id' => $client->id,
            'code_barre' => $item->code_barre,
        ];

        // Notifier tous les agents du centre
        $this->notifyCentre($centre, 'oneci_carte_prete', $title, $message, $data);

        // Notifier aussi l'agent Mayelia spécifique si défini
        if ($item->agent_mayelia_id) {
            return $this->notifyUser(
                $item->agentMayelia,
                'oneci_carte_prete',
                $title,
                $message,
                $data
            );
        }

        // Retourner une notification factice si aucun agent spécifique
        return new Notification([
            'type' => 'oneci_carte_prete',
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }
}


