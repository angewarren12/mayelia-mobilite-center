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
            width: 150mm;
            height: 70mm;
            border: 2px solid #028339;
            padding: 10mm;
            margin: 10mm auto;
            page-break-after: always;
            box-sizing: border-box;
        }
        .etiquette:last-child {
            page-break-after: auto;
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
                <img src="{{ asset('img/logo-oneci.jpg') }}" alt="Mayelia ONECI" class="logo-img" onerror="this.style.display='none';">
                <div>TRANSFERT ONECI - LOT #{{ $transfer->code_transfert }}</div>
            </div>
            <div class="etiquette-content">
                <div class="etiquette-left">
                    <div><strong>Item ID:</strong> {{ $item->id }}</div>
                    <div><strong>Client:</strong> {{ $item->dossierOuvert->rendezVous->client->nom_complet ?? 'N/A' }}</div>
                    <div><strong>Date transfert:</strong> {{ $transfer->date_envoi->format('d/m/Y') }}</div>
                </div>
                <div class="etiquette-right">
                    <div><strong>Destination:</strong> ONECI</div>
                    <div><strong>Statut:</strong> En attente</div>
                </div>
            </div>
            <div class="etiquette-barcode">
                {!! $item->barcode_svg !!}
                <div class="barcode-number">{{ $item->code_barre }}</div>
            </div>
            <div class="etiquette-footer">
                Code-barres unique pour suivi ONECI
            </div>
        </div>
    @endforeach
</body>
</html>


