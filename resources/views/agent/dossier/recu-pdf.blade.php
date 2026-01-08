<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu Mayelia - Dossier #{{ $dossierOuvert->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
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
        }
        
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #028339;
            margin-top: 10px;
        }
        
        .receipt-number {
            background-color: #028339;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px;
            text-align: center;
        }
        
        .section-title {
            font-weight: bold;
            color: #028339;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            font-size: 14px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        td {
            padding: 4px;
            vertical-align: top;
        }
        
        .label {
            width: 35%;
            font-weight: bold;
            color: #666;
            font-size: 9px;
        }
        
        .value {
            color: #333;
            font-size: 9px;
        }
        
        .box {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .bg-gray { background-color: #F3F4F6; }
        .bg-blue { background-color: #EFF6FF; }
        .bg-green { background-color: #F0FDF4; }
        
        .amount-box {
            background-color: #f0f9f4;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #028339;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        
        .signatures {
            width: 100%;
            margin-top: 40px;
        }
        
        .signature-box {
            width: 48%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <div style="margin-bottom: 15px;">
                @php
                    $logoPath = public_path('img/logo-oneci.jpg');
                    $logoExists = file_exists($logoPath);
                @endphp
                @if($logoExists)
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Mayelia Mobilité" class="logo-img">
                @endif
            </div>
            <div class="receipt-title">REÇU DE TRAÇABILITÉ - DOSSIER FINALISÉ</div>
            <div class="receipt-number">N° Dossier: {{ $dossierOuvert->id }}</div>
            <div style="margin-top: 10px; font-size: 11px;">Date d'émission : {{ now()->format('d/m/Y à H:i') }}</div>
        </div>

        <table style="width: 100%; margin-bottom: 0;">
            <tr>
                <!-- Colonne Gauche -->
                <td style="width: 49%; padding-right: 15px;">
                    <div class="section-title">Informations du Client</div>
                    <div class="box bg-gray">
                        <table>
                            <tr><td class="label">Nom complet :</td><td class="value">{{ $dossierOuvert->rendezVous->client->nom_complet ?? 'N/A' }}</td></tr>
                            <tr><td class="label">Email :</td><td class="value">{{ $dossierOuvert->rendezVous->client->email ?? 'N/A' }}</td></tr>
                            <tr><td class="label">Téléphone :</td><td class="value">{{ $dossierOuvert->rendezVous->client->telephone ?? 'N/A' }}</td></tr>
                            <tr><td class="label">Adresse :</td><td class="value">{{ $dossierOuvert->rendezVous->client->adresse ?? 'N/A' }}</td></tr>
                        </table>
                    </div>

                    <div class="section-title">Service et Formule</div>
                    <div class="box bg-blue">
                        <table>
                            <tr><td class="label">Service :</td><td class="value">{{ $dossierOuvert->rendezVous->service->nom ?? 'N/A' }}</td></tr>
                            <tr><td class="label">Formule :</td><td class="value">{{ $dossierOuvert->rendezVous->formule->nom ?? 'N/A' }}</td></tr>
                            <tr><td class="label">Centre :</td><td class="value">{{ $dossierOuvert->rendezVous->centre->nom ?? 'N/A' }}</td></tr>
                            <tr><td class="label">Ville :</td><td class="value">{{ $dossierOuvert->rendezVous->centre->ville->nom ?? 'N/A' }}</td></tr>
                        </table>
                    </div>
                </td>
                
                <!-- Colonne Droite -->
                <td style="width: 49%; padding-left: 15px; border-left: 1px solid #eee;">
                    <div class="section-title">Informations de Paiement</div>
                    <div class="box bg-green">
                        <table>
                            <tr><td class="label">Montant Total :</td><td class="value font-bold" style="color: #028339;">{{ number_format($dossierOuvert->paiementVerification->montant_paye ?? $dossierOuvert->rendezVous->formule->prix, 0, ',', ' ') }} FCFA</td></tr>
                            <tr><td class="label">Statut Paiement :</td><td class="value"><span style="background: #10B981; color: white; padding: 2px 8px; border-radius: 10px;">Payé</span></td></tr>
                            <tr><td class="label">Mode :</td><td class="value">Espèces / Guichet</td></tr>
                            <tr><td class="label">Référence :</td><td class="value">{{ $dossierOuvert->rendezVous->numero_suivi }}</td></tr>
                        </table>
                    </div>

                    <div class="amount-box">
                        <div class="amount-label">NET PAYÉ</div>
                        <div class="total-amount">{{ number_format($dossierOuvert->paiementVerification->montant_paye ?? $dossierOuvert->rendezVous->formule->prix, 0, ',', ' ') }} FCFA</div>
                    </div>
                    
                    <div class="box bg-gray" style="margin-top: 15px;">
                        <table>
                            <tr><td class="label">Géré par :</td><td class="value">{{ $dossierOuvert->agent->nom ?? 'Agent Mayelia' }}</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        
        <table class="signatures">
            <tr>
                <td class="signature-box">Signature du Client</td>
                <td style="width: 4%;"></td>
                <td class="signature-box">Signature Agent</td>
            </tr>
        </table>

        <div class="footer">
            <p><strong>Note Importante :</strong> Ce reçu est une preuve de finalisation de votre dossier au centre Mayelia.</p>
            <p>Conservez ce document précieusement. Pour toute question, contactez notre support.</p>
            <p style="margin-top: 10px;">Mayelia Mobilité - Centre de gestion des démarches administratives<br>
            www.mayelia.ci - contact@mayelia.ci</p>
        </div>
    </div>
</body>
</html>
