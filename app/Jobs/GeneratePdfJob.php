<?php

namespace App\Jobs;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $view,
        public array $data,
        public string $filename,
        public ?string $disk = null,
        public string $path = 'pdfs'
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $pdf = Pdf::loadView($this->view, $this->data);
            
            $disk = $this->disk ?? config('filesystems.default');
            $fullPath = $this->path . '/' . $this->filename;
            
            Storage::disk($disk)->put($fullPath, $pdf->output());
            
            Log::info('PDF généré avec succès', [
                'filename' => $this->filename,
                'path' => $fullPath,
                'disk' => $disk,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du PDF', [
                'filename' => $this->filename,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job GeneratePdfJob a échoué', [
            'filename' => $this->filename,
            'error' => $exception->getMessage(),
        ]);
    }
}

