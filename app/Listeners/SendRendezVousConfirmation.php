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

        // Envoi SMS de confirmation si numéro présent
        if ($rendezVous->client_telephone) {
            $message = "Votre rendez-vous Mayelia est confirmé. Code: {$rendezVous->code_confirmation}. Date: {$rendezVous->date_rdv->format('d/m/Y')} à {$rendezVous->heure_rdv}.";
            \App\Jobs\SendSmsJob::dispatch($rendezVous->client_telephone, $message);
        }

        // Envoi Email de confirmation si email présent
        if ($rendezVous->client_email) {
            \App\Jobs\SendEmailJob::dispatch(
                $rendezVous->client_email,
                'Confirmation de votre rendez-vous - Mayelia',
                'emails.rendez-vous-confirmation', // Assurez-vous que cette vue existe
                ['rendezVous' => $rendezVous]
            );
        }
    }
}

