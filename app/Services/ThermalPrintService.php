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
     * Génère des commandes ESC/POS pour impression directe
     * 
     * Note: Cette méthode est optionnelle car l'impression est actuellement gérée
     * via la vue Blade 'qms.ticket-print' qui génère le HTML/JS pour l'impression côté client.
     * 
     * Cette méthode pourrait être utilisée pour :
     * - Impression serveur directe (si imprimante connectée au serveur)
     * - API d'impression centralisée
     * - Intégration avec système d'impression réseau
     * 
     * Format ESC/POS: https://reference.epson-biz.com/modules/ref_escpos/
     * 
     * @param Ticket $ticket
     * @return string Commandes ESC/POS en binaire (format prêt pour socket/port série)
     */
    public function generateEscPosCommands(Ticket $ticket): string
    {
        // NOTE: Non implémenté car non nécessaire actuellement
        // L'impression est gérée via la vue Blade qui génère le format d'impression
        // pour les imprimantes thermiques via JavaScript côté client (kiosk web)
        // ou via l'application Flutter (kiosk mobile)
        // 
        // Pour une implémentation future, voir:
        // - https://github.com/mike42/escpos-php (bibliothèque PHP pour ESC/POS)
        // - Documentation ESC/POS: https://reference.epson-biz.com/modules/ref_escpos/
        
        $commands = '';
        
        // Exemple de structure future :
        // $commands .= "\x1B\x40"; // Initialize printer (ESC @)
        // $commands .= "\x1B\x61\x01"; // Center align (ESC a 1)
        // $commands .= "\x1B\x21\x10"; // Double height (ESC ! 16)
        // $commands .= $ticket->centre->nom . "\n";
        // $commands .= "\x1B\x21\x00"; // Normal size
        // $commands .= "TICKET : " . $ticket->numero . "\n";
        // ... etc
        // $commands .= "\x1D\x56\x41"; // Cut paper (GS V 65)
        
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

