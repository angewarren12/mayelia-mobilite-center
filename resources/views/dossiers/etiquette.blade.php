<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étiquette - Dossier {{ $dossierOuvert->id }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .etiquette {
            width: 150mm;
            height: 70mm;
            border: 2px solid #028339;
            padding: 10mm;
            margin: 10mm auto;
            page-break-inside: avoid;
            box-sizing: border-box;
        }
        .etiquette-header {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5mm;
            border-bottom: 2px solid #028339;
            padding-bottom: 3mm;
            text-align: center;
            color: #028339;
        }
        .logo-img {
            max-width: 80px;
            height: auto;
            margin-bottom: 3mm;
        }
        .etiquette-content {
            font-size: 10pt;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5mm;
        }
        .etiquette-left {
            flex: 1;
        }
        .etiquette-right {
            flex: 1;
            text-align: right;
        }
        .etiquette-barcode {
            text-align: center;
            margin: 5mm 0;
            padding: 5mm;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .barcode-number {
            font-family: 'Courier New', monospace;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 3mm;
        }
        .etiquette-footer {
            font-size: 8pt;
            margin-top: 5mm;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 3mm;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 20px; text-align: center;">
        <h1>Étiquette du dossier #{{ $dossierOuvert->id }}</h1>
        <p>Code-barres: {{ $codeBarre }}</p>
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="{{ route('dossiers.index') }}" style="display: inline-block; padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 5px;">
            Retour
        </a>
    </div>

    <div class="etiquette">
        <div class="etiquette-header">
            <img src="{{ asset('img/logo-oneci.jpg') }}" alt="Mayelia Mobilité" class="logo-img" onerror="this.style.display='none';">
            <div>MAYELIA MOBILITÉ - ÉTIQUETTE DOSSIER</div>
        </div>
        <div class="etiquette-content">
            <div class="etiquette-left">
                <div><strong>N° Dossier:</strong> {{ $dossierOuvert->id }}</div>
                <div><strong>Centre:</strong> {{ $dossierOuvert->rendezVous->centre->nom ?? 'N/A' }}</div>
                <div><strong>Service:</strong> {{ $dossierOuvert->rendezVous->service->nom ?? 'N/A' }}</div>
            </div>
            <div class="etiquette-right">
                <div><strong>Date:</strong> {{ $dossierOuvert->date_ouverture->format('d/m/Y') }}</div>
                <div><strong>Client:</strong> {{ $dossierOuvert->rendezVous->client->nom_complet ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="etiquette-barcode">
            {!! $barcodeSvg !!}
            <div class="barcode-number">{{ $codeBarre }}</div>
        </div>
        <div class="etiquette-footer">
            Code-barres pour suivi et traçabilité du dossier
        </div>
    </div>
</body>
</html>

