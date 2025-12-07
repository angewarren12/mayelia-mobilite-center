<?php

namespace App\Services;

use App\Models\DossierOneciTransfer;
use App\Mail\OneciTransferMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class OneciEmailService
{
    protected $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Envoyer l'email à ONECI avec le PDF en pièce jointe
     */
    public function sendTransferEmail(DossierOneciTransfer $transfer): bool
    {
        try {
            $emailOneci = config('services.oneci.email', env('ONECI_EMAIL'));
            
            if (!$emailOneci) {
                \Log::error('Email ONECI non configuré');
                return false;
            }

            // Générer le PDF
            $pdf = $this->generateTransferPdf($transfer);

            // Envoyer l'email avec le PDF en pièce jointe
            Mail::to($emailOneci)->send(new OneciTransferMail($transfer, $pdf));

            \Log::info('Email envoyé à ONECI pour le transfert ' . $transfer->code_transfert);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de l\'email ONECI: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Générer le PDF du transfert avec tous les détails des dossiers
     */
    public function generateTransferPdf(DossierOneciTransfer $transfer)
    {
        // Charger toutes les relations nécessaires
        $transfer->load([
            'centre.ville',
            'agentMayelia',
            'items.dossierOuvert.rendezVous.client',
            'items.dossierOuvert.rendezVous.service',
            'items.dossierOuvert.rendezVous.formule',
            'items.dossierOuvert.documentVerifications.documentRequis',
            'items.dossierOuvert.paiementVerification',
            'items.dossierOuvert.agent'
        ]);

        // Générer les codes-barres SVG pour chaque item
        $items = $transfer->items->map(function($item) {
            $item->barcode_svg = $this->barcodeService->generateCode128SVG($item->code_barre);
            return $item;
        });

        // Générer le PDF
        $pdf = Pdf::loadView('oneci-transfers.email-pdf', [
            'transfer' => $transfer,
            'items' => $items
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        return $pdf;
    }
}


