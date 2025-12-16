<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Réservation - {{ $rendezVous->numero_suivi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #028339;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-img {
            max-width: 200px;
            height: auto;
            margin-bottom: 10px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #028339;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 18px;
            color: #333;
            margin: 10px 0;
        }
        .receipt-subtitle {
            color: #666;
            font-size: 12px;
        }
        .receipt-number {
            background: #028339;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            margin: 15px auto;
            text-align: center;
            display: inline-block;
            width: 100%;
        }
        .section {
            margin: 20px 0;
        }
        .section-title {
            font-weight: bold;
            color: #028339;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            font-size: 14px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .info-value {
            color: #333;
            text-align: right;
        }
        .total-section {
            background: #f0f9f4;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #028339;
            text-align: center;
            margin-top: 10px;
        }
        .qr-section {
            text-align: center;
            margin: 20px 0;
        }
        .qr-placeholder {
            width: 100px;
            height: 100px;
            border: 2px solid #028339;
            display: inline-block;
            line-height: 100px;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body { background: white; }
            .receipt-container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- En-tête -->
        <div class="header">
            @php
                $logoPath = public_path('img/logo-oneci.jpg');
                $logoExists = file_exists($logoPath);
            @endphp
            @if($logoExists)
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Mayelia Mobilité" class="logo-img">
            @else
                <div class="logo">MAYELIA MOBILITÉ</div>
            @endif
            <div class="receipt-title">REÇU DE RÉSERVATION</div>
            <div class="receipt-subtitle">Confirmation de votre rendez-vous</div>
        </div>

        <!-- Numéro de suivi -->
        <div class="receipt-number">
            {{ $rendezVous->numero_suivi }}
        </div>

        <div class="section">
            <div class="section-title">DÉTAILS DE LA RÉSERVATION</div>
            <div class="info-row">
                <span class="info-label">Centre:</span>
                <span class="info-value">{{ $rendezVous->centre->nom }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Service:</span>
                <span class="info-value">{{ $rendezVous->service->nom }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Formule:</span>
                <span class="info-value">{{ $rendezVous->formule->nom }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($rendezVous->date_rendez_vous)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Heure:</span>
                <span class="info-value">{{ $rendezVous->tranche_horaire }}</span>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">INFORMATIONS CLIENT</div>
            <div class="info-row">
                <span class="info-label">Nom:</span>
                <span class="info-value">{{ $rendezVous->client->nom }} {{ $rendezVous->client->prenom }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $rendezVous->client->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Téléphone:</span>
                <span class="info-value">{{ $rendezVous->client->telephone }}</span>
            </div>
        </div>
        
        <div class="total-section">
            <div style="text-align: center; color: #666;">Montant à payer</div>
            <div class="total-amount">{{ number_format($rendezVous->formule->prix, 0, ',', ' ') }} FCFA</div>
        </div>
        
        <div class="qr-section">
            <div class="qr-placeholder">QR CODE</div>
            @if(isset($qrCodeBase64))
                <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="width: 100px; height: 100px; margin-top: 10px;">
            @endif
            <div style="margin-top: 5px; font-size: 10px; color: #666;">{{ $rendezVous->numero_suivi }}</div>
        </div>
        
        <div class="footer">
            <p><strong>Merci pour votre confiance !</strong></p>
            <p>Ce reçu confirme votre réservation. Veuillez le présenter lors de votre rendez-vous.</p>
            <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>
