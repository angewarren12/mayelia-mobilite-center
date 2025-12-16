<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class SmsService
{
    /**
     * Envoyer un SMS
     * 
     * Note: Service préparé pour intégration future d'un fournisseur SMS (Twilio, Nexmo, etc.)
     * Configuration recommandée dans config/services.php :
     * 'sms' => [
     *     'driver' => env('SMS_DRIVER', 'log'), // 'log', 'twilio', 'nexmo'
     *     'twilio' => [
     *         'account_sid' => env('TWILIO_ACCOUNT_SID'),
     *         'auth_token' => env('TWILIO_AUTH_TOKEN'),
     *         'from' => env('TWILIO_FROM_NUMBER'),
     *     ],
     * ]
     * 
     * @param string $phone Numéro de téléphone (format international)
     * @param string $message Message à envoyer
     * @return bool Succès de l'envoi
     */
    public function sendSms(string $phone, string $message): bool
    {
        $driver = config('services.sms.driver', 'log');

        switch ($driver) {
            case 'log':
                // Mode développement : logger uniquement
                Log::info('SMS à envoyer', [
                    'phone' => $phone,
                    'message' => $message,
                    'timestamp' => now()
                ]);
                return true;

            // TODO: Implémenter les autres drivers
            // case 'twilio':
            //     return $this->sendViaTwilio($phone, $message);
            // case 'nexmo':
            //     return $this->sendViaNexmo($phone, $message);

            default:
                Log::warning('Driver SMS non implémenté', ['driver' => $driver]);
                return false;
        }
    }

    // TODO: Implémenter sendViaTwilio() pour intégration Twilio
    // TODO: Implémenter sendViaNexmo() pour intégration Nexmo/Vonage
}


