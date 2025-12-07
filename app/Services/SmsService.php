<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoyer un SMS (structure préparée pour intégration future)
     * Pour l'instant, logger le message
     */
    public function sendSms(string $phone, string $message): bool
    {
        // TODO: Intégrer un service SMS (Twilio, Nexmo, etc.)
        // Pour l'instant, on log juste le message
        
        Log::info('SMS à envoyer', [
            'phone' => $phone,
            'message' => $message,
            'timestamp' => now()
        ]);

        // Simuler l'envoi
        return true;
    }
}


