<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étiquette Dossier #{{ $dossierOuvert->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            padding: 10mm;
            background: white;
        }
        
        .etiquette {
            width: 95mm;
            height: 138mm;
            border: 2px solid #2D3748;
            padding: 8mm;
            position: relative;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #4299E1;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }
        
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #4299E1;
            margin-bottom: 2mm;
        }
        
        .titre {
            font-size: 14px;
            font-weight: bold;
            color: #2D3748;
            margin-bottom: 1mm;
        }
        
        .numero-dossier {
            font-size: 18px;
            font-weight: bold;
            color: #4299E1;
            margin-top: 2mm;
        }
        
        .info-section {
            margin-bottom: 4mm;
        }
        
        .info-label {
            font-size: 9px;
            color: #718096;
            text-transform: uppercase;
            margin-bottom: 1mm;
            font-weight: bold;
        }
        
        .info-value {
            font-size: 11px;
            color: #2D3748;
            font-weight: 600;
            padding: 2mm;
            background: #F7FAFC;
            border-left: 3px solid #4299E1;
        }
        
        .barcode-section {
            text-align: center;
            margin-top: 5mm;
            padding: 3mm;
            background: #F7FAFC;
            border: 1px dashed #CBD5E0;
        }
        
        .barcode {
            font-size: 14px;
            font-weight: bold;
            color: #2D3748;
            margin: 1mm 0;
            text-align: center;
        }
        
        .barcode-label {
            font-size: 8px;
            color: #718096;
            text-transform: uppercase;
        }
        
        .footer {
            position: absolute;
            bottom: 5mm;
            left: 8mm;
            right: 8mm;
            text-align: center;
            font-size: 7px;
            color: #A0AEC0;
            border-top: 1px solid #E2E8F0;
            padding-top: 2mm;
        }
        
        .service-badge {
            display: inline-block;
            background: #4299E1;
            color: white;
            padding: 1mm 3mm;
            border-radius: 3mm;
            font-size: 9px;
            font-weight: bold;
            margin-top: 2mm;
        }
    </style>
</head>
<body>
    <div class="etiquette">
        <!-- Header -->
        <div class="header">
            <div class="logo">MAYELIA MOBILITÉ</div>
            <div class="titre">ÉTIQUETTE DE TRAÇABILITÉ</div>
            <div class="numero-dossier">#{{ str_pad($dossierOuvert->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        
        <!-- Informations Client -->
        <div class="info-section">
            <div class="info-label">Client</div>
            <div class="info-value">
                {{ strtoupper($dossierOuvert->rendezVous->client->nom) }} 
                {{ ucfirst($dossierOuvert->rendezVous->client->prenom) }}
            </div>
        </div>
        
        <!-- Service -->
        <div class="info-section">
            <div class="info-label">Service</div>
            <div class="info-value">
                {{ $dossierOuvert->rendezVous->service->nom }}
                <div class="service-badge">{{ $dossierOuvert->rendezVous->formule->nom }}</div>
            </div>
        </div>
        
        <!-- Centre -->
        <div class="info-section">
            <div class="info-label">Centre</div>
            <div class="info-value">{{ $dossierOuvert->rendezVous->centre->nom }}</div>
        </div>
        
        <!-- Date -->
        <div class="info-section">
            <div class="info-label">Date de finalisation</div>
            <div class="info-value">{{ now()->format('d/m/Y à H:i') }}</div>
        </div>
        
        <!-- Code-barres -->
        <div class="barcode-section">
            <div class="barcode-label">Code de traçabilité</div>
            <div style="margin: 3mm 0; text-align: center;">
                @php
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    $barcode = base64_encode($generator->getBarcode($dossierOuvert->code_barre, $generator::TYPE_CODE_128));
                @endphp
                <img src="data:image/png;base64,{{ $barcode }}" style="width: 60mm; height: 15mm; display: block; margin: 0 auto;">
            </div>
            <div class="barcode" style="border: none; font-size: 14px; margin: 1mm 0;">{{ $dossierOuvert->code_barre }}</div>
            <div class="barcode-label">Scannez pour suivre le dossier</div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Document généré le {{ now()->format('d/m/Y à H:i') }} par {{ Auth::user()->nom }}
        </div>
    </div>
</body>
</html>
