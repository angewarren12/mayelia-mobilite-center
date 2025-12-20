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
        
        @page {
            margin: 0;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 2mm;
            width: 80mm;
            height: 60mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
        }
        
        .container {
            width: 76mm;
            height: 56mm;
            border: 1px solid #000;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }
        
        .header {
            width: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            border-bottom: 0.5px solid #000;
            padding-bottom: 1mm;
            margin-bottom: 1mm;
        }
        
        .logo-img {
            height: 10mm;
            width: auto;
        }
        
        .dossier-id {
            font-size: 14pt;
            font-weight: bold;
        }
        
        .content {
            width: 100%;
            text-align: left;
            flex-grow: 1;
        }
        
        .client-name {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .service-info {
            font-size: 9pt;
            margin-bottom: 1mm;
        }
        
        .center-info {
            font-size: 8pt;
            color: #444;
        }
        
        .barcode-section {
            width: 100%;
            text-align: center;
            margin-top: 1mm;
        }
        
        .barcode-img {
            width: 60mm;
            height: 12mm;
        }
        
        .barcode-value {
            font-size: 9pt;
            font-family: monospace;
            margin-top: 0.5mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @php
                $logoPath = public_path('img/logo.png');
                if (!file_exists($logoPath)) {
                    $logoPath = public_path('img/logo-oneci.jpg');
                }
                $logoBase64 = base64_encode(file_get_contents($logoPath));
                $mimeType = mime_content_type($logoPath);
            @endphp
            <img src="data:{{ $mimeType }};base64,{{ $logoBase64 }}" class="logo-img">
            <div class="dossier-id">#{{ str_pad($dossierOuvert->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        
        <div class="content">
            <div class="client-name">{{ strtoupper($dossierOuvert->rendezVous->client->nom) }} {{ strtoupper($dossierOuvert->rendezVous->client->prenom) }}</div>
            <div class="service-info">{{ $dossierOuvert->rendezVous->service->nom }} ({{ $dossierOuvert->rendezVous->formule->nom }})</div>
            <div class="center-info">Centre: {{ $dossierOuvert->rendezVous->centre->nom }} | {{ now()->format('d/m/Y H:i') }}</div>
        </div>
        
        <div class="barcode-section">
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode = base64_encode($generator->getBarcode($dossierOuvert->code_barre, $generator::TYPE_CODE_128));
            @endphp
            <img src="data:image/png;base64,{{ $barcode }}" class="barcode-img">
            <div class="barcode-value">{{ $dossierOuvert->code_barre }}</div>
        </div>
    </div>
</body>
</html>
