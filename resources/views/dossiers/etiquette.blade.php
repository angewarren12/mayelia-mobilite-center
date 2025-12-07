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
            border: 1px solid #000;
            padding: 5mm;
            margin: 5mm;
            display: inline-block;
            page-break-inside: avoid;
            box-sizing: border-box;
        }
        .etiquette-header {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 3mm;
            border-bottom: 1px solid #000;
            padding-bottom: 2mm;
            text-align: center;
        }
        .etiquette-content {
            font-size: 9pt;
        }
        .etiquette-barcode {
            text-align: center;
            margin: 2mm 0;
        }
        .etiquette-footer {
            font-size: 8pt;
            margin-top: 2mm;
            text-align: center;
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
            MAYELIA MOBILITÉ - ONECI
        </div>
        <div class="etiquette-content">
            <div><strong>Code-barres:</strong></div>
            <div class="etiquette-barcode">
                {!! $barcodeSvg !!}
            </div>
            <div style="text-align: center; font-family: monospace; font-size: 10pt; margin-top: 2mm;">
                {{ $codeBarre }}
            </div>
            <div style="margin-top: 3mm;">
                <strong>Client:</strong> {{ $dossierOuvert->rendezVous->client->nom_complet ?? 'N/A' }}<br>
                <strong>Service:</strong> {{ $dossierOuvert->rendezVous->service->nom ?? 'N/A' }}<br>
                <strong>N° Dossier:</strong> DOS-{{ str_pad($dossierOuvert->id, 8, '0', STR_PAD_LEFT) }}
            </div>
        </div>
        <div class="etiquette-footer">
            Date: {{ $dossierOuvert->date_ouverture->format('d/m/Y') }}
        </div>
    </div>
</body>
</html>

