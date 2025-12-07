<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étiquettes - {{ $transfer->code_transfert }}</title>
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
            width: 90mm;
            height: 50mm;
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
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 20px; text-align: center;">
        <h1>Étiquettes pour transfert {{ $transfer->code_transfert }}</h1>
        <p>Nombre d'étiquettes: {{ $items->count() }}</p>
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="{{ route('oneci-transfers.show', $transfer) }}" style="display: inline-block; margin-left: 10px; padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 5px;">
            Retour
        </a>
    </div>

    @foreach($items as $item)
        <div class="etiquette">
            <div class="etiquette-header">
                MAYELIA MOBILITÉ - ONECI
            </div>
            <div class="etiquette-content">
                <div><strong>Code-barres:</strong></div>
                <div class="etiquette-barcode">
                    {!! $item->barcode_svg !!}
                </div>
                <div style="text-align: center; font-family: monospace; font-size: 10pt; margin-top: 2mm;">
                    {{ $item->code_barre }}
                </div>
                <div style="margin-top: 3mm;">
                    <strong>Client:</strong> {{ $item->dossierOuvert->rendezVous->client->nom_complet ?? 'N/A' }}<br>
                    <strong>Service:</strong> {{ $item->dossierOuvert->rendezVous->service->nom ?? 'N/A' }}<br>
                    <strong>Transfert:</strong> {{ $transfer->code_transfert }}
                </div>
            </div>
            <div class="etiquette-footer">
                Date: {{ $transfer->date_envoi->format('d/m/Y') }}
            </div>
        </div>
    @endforeach
</body>
</html>


