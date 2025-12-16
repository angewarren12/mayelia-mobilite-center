<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $to,
        public string $subject,
        public string $view,
        public array $data = []
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // TODO: Implémenter l'envoi d'email réel
            // Pour l'instant, on log seulement
            Log::info('Email à envoyer', [
                'to' => $this->to,
                'subject' => $this->subject,
                'view' => $this->view,
            ]);

            // Exemple d'implémentation future :
            // Mail::to($this->to)->send(new $this->view($this->data));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email', [
                'to' => $this->to,
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
        Log::error('Job SendEmailJob a échoué', [
            'to' => $this->to,
            'error' => $exception->getMessage(),
        ]);
    }
}

