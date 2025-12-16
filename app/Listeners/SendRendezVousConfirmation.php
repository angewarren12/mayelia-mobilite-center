<?php

namespace App\Listeners;

use App\Events\RendezVousCreated;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class SendRendezVousConfirmation
{
    protected $smsService;

    /**
     * Create the event listener.
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     */
    public function handle(RendezVousCreated $event): void
    {
        $rendezVous = $event->rendezVous;
        
        // Log de confirmation
        Log::info('Rendez-vous créé', [
            'rendez_vous_id' => $rendezVous->id,
            'numero_suivi' => $rendezVous->numero_suivi,
            'client' => $rendezVous->client_nom . ' ' . $rendezVous->client_prenom,
        ]);

        // TODO: Envoyer SMS/Email de confirmation si nécessaire
        // Pour l'instant, on log seulement
        // $this->smsService->sendSms($rendezVous->client_telephone, $message);
    }
}

