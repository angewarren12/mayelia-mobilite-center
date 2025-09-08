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
            padding: 10px;
            background-color: #f5f5f5;
            font-size: 12px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 3px;
        }
        .receipt-subtitle {
            color: #6b7280;
            font-size: 12px;
        }
        .receipt-number {
            background: #3b82f6;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
            text-align: center;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .info-value {
            color: #1f2937;
            font-size: 16px;
        }
        .client-info {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        .appointment-details {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #0ea5e9;
        }
        .total-section {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
            text-align: center;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirme {
            background: #dcfce7;
            color: #166534;
        }
        .qr-placeholder {
            width: 100px;
            height: 100px;
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- En-tête -->
        <div class="header">
            <div class="logo">MAYELIA</div>
            <div class="receipt-title">Reçu de Réservation</div>
            <div class="receipt-subtitle">Confirmation de votre rendez-vous</div>
        </div>

        <!-- Numéro de suivi -->
        <div class="receipt-number">
            {{ $rendezVous->numero_suivi }}
        </div>

        <!-- Informations client -->
        <div class="section">
            <div class="section-title">Informations Client</div>
            <div class="client-info">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nom complet</div>
                        <div class="info-value">{{ $rendezVous->client->nom }} {{ $rendezVous->client->prenom }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">{{ $rendezVous->client->telephone }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $rendezVous->client->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date de naissance</div>
                        <div class="info-value">{{ $rendezVous->client->date_naissance ? \Carbon\Carbon::parse($rendezVous->client->date_naissance)->format('d/m/Y') : 'Non renseignée' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails du rendez-vous -->
        <div class="section">
            <div class="section-title">Détails du Rendez-vous</div>
            <div class="appointment-details">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Centre</div>
                        <div class="info-value">{{ $rendezVous->centre->nom }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Service</div>
                        <div class="info-value">{{ $rendezVous->service->nom }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Formule</div>
                        <div class="info-value">{{ $rendezVous->formule->nom }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Statut</div>
                        <div class="info-value">
                            <span class="status-badge status-confirme">{{ $rendezVous->statut_formate }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($rendezVous->date_rendez_vous)->format('l d F Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Heure</div>
                        <div class="info-value">{{ $rendezVous->tranche_horaire }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Montant -->
        <div class="section">
            <div class="total-section">
                <div class="info-label">Montant à payer</div>
                <div class="total-amount">{{ number_format($rendezVous->formule->prix, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>

        <!-- QR Code placeholder -->
        <div class="qr-placeholder">
            <div style="text-align: center; color: #6b7280;">
                <div style="font-size: 12px;">QR Code</div>
                <div style="font-size: 10px;">{{ $rendezVous->numero_suivi }}</div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <p><strong>Merci pour votre confiance !</strong></p>
            <p>Ce reçu confirme votre réservation. Veuillez le présenter lors de votre rendez-vous.</p>
            <p>Pour toute question, contactez-nous au +225 XX XX XX XX</p>
            <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>
