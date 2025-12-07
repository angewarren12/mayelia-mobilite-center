<?php

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\DossierOuvert;

class BarcodeService
{
    /**
     * Générer un code-barres Code 128 en SVG
     */
    public function generateCode128SVG(string $code): string
    {
        $generator = new BarcodeGeneratorSVG();
        return $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 50);
    }

    /**
     * Générer un code-barres Code 128 en PNG (base64)
     */
    public function generateCode128PNG(string $code): string
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 50);
        return 'data:image/png;base64,' . base64_encode($barcode);
    }

    /**
     * Générer un code-barres pour un dossier
     */
    public function generateCodeBarreForDossier(DossierOuvert $dossier): string
    {
        // Si le dossier a déjà un code-barres, le retourner
        if ($dossier->code_barre) {
            return $dossier->code_barre;
        }

        // Générer un nouveau code-barres basé sur l'ID du dossier
        $baseCode = 'DOS-' . str_pad($dossier->id, 8, '0', STR_PAD_LEFT);
        
        // Vérifier l'unicité dans dossier_ouvert et dossier_oneci_items
        do {
            $code = $baseCode . '-' . strtoupper(substr(uniqid(), -4));
        } while (
            \App\Models\DossierOuvert::where('code_barre', $code)->exists() ||
            \App\Models\DossierOneciItem::where('code_barre', $code)->exists()
        );
        
        // Mettre à jour le dossier
        $dossier->update(['code_barre' => $code]);

        return $code;
    }

    /**
     * Générer le SVG du code-barres pour un dossier
     */
    public function getBarcodeSVGForDossier(DossierOuvert $dossier): string
    {
        $code = $this->generateCodeBarreForDossier($dossier);
        return $this->generateCode128SVG($code);
    }

    /**
     * Générer le PNG (base64) du code-barres pour un dossier
     */
    public function getBarcodePNGForDossier(DossierOuvert $dossier): string
    {
        $code = $this->generateCodeBarreForDossier($dossier);
        return $this->generateCode128PNG($code);
    }
}

