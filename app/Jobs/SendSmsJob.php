<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $phone,
        public string $message
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        try {
            $smsService->sendSms($this->phone, $this->message);
            
            Log::info('SMS envoyé avec succès', [
                'phone' => $this->phone,
                'message_length' => strlen($this->message),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS', [
                'phone' => $this->phone,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Relancer pour que le job soit marqué comme failed
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job SendSmsJob a échoué', [
            'phone' => $this->phone,
            'error' => $exception->getMessage(),
        ]);
    }
}

