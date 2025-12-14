<?php

namespace App\Services;

use App\Models\Ticket;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ThermalPrintService
{
    /**
     * Dimensions de l'imprimante thermique 58mm
     */
    const PAPER_WIDTH = 58; // mm - Largeur du papier
    const PRINT_WIDTH = 48; // mm - Largeur d'impression effective
    const QR_CODE_SIZE = 140; // pixels pour SVG (optimisé pour 48mm de largeur)

    /**
     * Génère les données du QR code pour un ticket
     * 
     * @param Ticket $ticket
     * @return string Format: TICKET:{numero}:{centre_id}
     */
    public function generateTicketQrCodeData(Ticket $ticket): string
    {
        return 'TICKET:' . $ticket->numero . ':' . $ticket->centre_id;
    }

    /**
     * Génère le QR code en SVG encodé en base64 pour un ticket
     * 
     * @param Ticket $ticket
     * @return string Base64 encoded SVG
     */
    public function generateTicketQrCode(Ticket $ticket): string
    {
        $qrCodeData = $this->generateTicketQrCodeData($ticket);
        
        $qrCodeSvg = QrCode::size(self::QR_CODE_SIZE)
            ->format('svg')
            ->errorCorrection('M') // Correction d'erreur moyenne pour meilleure lisibilité
            ->generate($qrCodeData);
        
        return 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
    }

    /**
     * Prépare les données pour l'impression d'un ticket
     * 
     * @param Ticket $ticket
     * @return array Données formatées pour la vue
     */
    public function prepareTicketPrintData(Ticket $ticket): array
    {
        return [
            'ticket' => $ticket,
            'qrCodeBase64' => $this->generateTicketQrCode($ticket),
            'paperWidth' => self::PAPER_WIDTH,
            'printWidth' => self::PRINT_WIDTH,
        ];
    }

    /**
     * Valide un QR code de ticket
     * 
     * @param string $qrCodeData
     * @return array|null ['numero' => string, 'centre_id' => int] ou null si invalide
     */
    public function validateTicketQrCode(string $qrCodeData): ?array
    {
        if (!preg_match('/^TICKET:([^:]+):(\d+)$/', $qrCodeData, $matches)) {
            return null;
        }

        return [
            'numero' => $matches[1],
            'centre_id' => (int) $matches[2],
        ];
    }

    /**
     * Génère des commandes ESC/POS pour impression directe (future implémentation)
     * 
     * @param Ticket $ticket
     * @return string Commandes ESC/POS en binaire
     */
    public function generateEscPosCommands(Ticket $ticket): string
    {
        // TODO: Implémenter la génération de commandes ESC/POS
        // Pour l'instant, retourne une chaîne vide
        // Format ESC/POS: https://reference.epson-biz.com/modules/ref_escpos/
        
        $commands = '';
        
        // Exemple de structure future :
        // $commands .= "\x1B\x40"; // Initialize printer
        // $commands .= "\x1B\x61\x01"; // Center align
        // $commands .= "ONECI - " . $ticket->centre->nom . "\n";
        // ... etc
        
        return $commands;
    }

    /**
     * Calcule la taille optimale de police pour l'impression thermique
     * 
     * @param int $charCount Nombre de caractères
     * @return int Taille de police en pixels
     */
    public function calculateOptimalFontSize(int $charCount): int
    {
        $maxCharsPerLine = 24; // Caractères par ligne pour 48mm
        
        if ($charCount <= $maxCharsPerLine) {
            return 11;
        } elseif ($charCount <= $maxCharsPerLine * 2) {
            return 10;
        } else {
            return 9;
        }
    }
}

