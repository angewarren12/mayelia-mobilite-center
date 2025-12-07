<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu Mayelia - Dossier #{{ $dossierOuvert->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
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
            border-bottom: 2px solid #1E40AF;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .logo-container {
            margin-bottom: 8px;
        }
        
        .logo-img {
            max-width: 150px;
            height: auto;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1E40AF;
            margin-bottom: 3px;
        }
        
        .company-subtitle {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            color: #1E40AF;
            margin-top: 8px;
        }
        
        .receipt-number {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }
        
        .section {
            margin-bottom: 12px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1E40AF;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #666;
            width: 35%;
            padding: 4px 0;
            font-size: 9px;
        }
        
        .info-value {
            display: table-cell;
            color: #333;
            padding: 4px 0;
            font-size: 9px;
        }
        
        .client-info {
            background-color: #F3F4F6;
            padding: 8px;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        
        .service-info {
            background-color: #EFF6FF;
            padding: 8px;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        
        .payment-info {
            background-color: #F0FDF4;
            padding: 8px;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        
        .amount-box {
            background-color: #1E40AF;
            color: white;
            padding: 12px;
            border-radius: 3px;
            text-align: center;
            margin: 12px 0;
        }
        
        .amount-label {
            font-size: 10px;
            margin-bottom: 3px;
        }
        
        .amount-value {
            font-size: 20px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 9px;
            background-color: #10B981;
            color: white;
        }
        
        .date-info {
            text-align: right;
            color: #666;
            font-size: 9px;
            margin-bottom: 10px;
        }
        
        .signature-section {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 3px;
            font-size: 9px;
        }
        
        .two-columns {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <div class="logo-container">
                <img src="https://www.mayeliamobilite.com/LOGO-MOBILITE.png" alt="Mayelia Mobilité" class="logo-img">
            </div>
            <div class="company-name">MAYELIA MOBILITÉ</div>
            <div class="company-subtitle">Solutions de mobilité et d'identification</div>
            <div class="receipt-title">REÇU DE TRAÇABILITÉ</div>
            <div class="receipt-number">N° {{ $dossierOuvert->id }}/{{ now()->format('Y') }}</div>
        </div>
        
        <div class="date-info">
            Date d'émission : {{ now()->format('d/m/Y à H:i') }}
        </div>
        
        <!-- Deux colonnes pour optimiser l'espace -->
        <div class="two-columns">
            <!-- Colonne gauche -->
            <div class="column">
                <!-- Informations du client -->
                <div class="section">
                    <div class="section-title">Informations du Client</div>
                    <div class="client-info">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Nom complet :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->client->nom_complet ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->client->email ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Téléphone :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->client->telephone ?? 'N/A' }}</div>
                            </div>
                            @if($dossierOuvert->rendezVous->client->date_naissance)
                            <div class="info-row">
                                <div class="info-label">Date de naissance :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->client->date_naissance->format('d/m/Y') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Informations du service -->
                <div class="section">
                    <div class="section-title">Service et Formule</div>
                    <div class="service-info">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Service :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->service->nom ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Formule :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->formule->nom ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Centre :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->centre->nom ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Ville :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->centre->ville->nom ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Date RDV :</div>
                                <div class="info-value">{{ $dossierOuvert->rendezVous->date_rendez_vous->format('d/m/Y') }} {{ $dossierOuvert->rendezVous->tranche_horaire }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Colonne droite -->
            <div class="column">
        
                <!-- Informations de paiement -->
                @if($dossierOuvert->paiementVerification)
                <div class="section">
                    <div class="section-title">Informations de Paiement</div>
                    <div class="payment-info">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Montant payé :</div>
                                <div class="info-value">{{ number_format($dossierOuvert->paiementVerification->montant_paye, 0, ',', ' ') }} FCFA</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Mode de paiement :</div>
                                <div class="info-value">{{ ucfirst($dossierOuvert->paiementVerification->mode_paiement ?? 'N/A') }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Référence :</div>
                                <div class="info-value">{{ $dossierOuvert->paiementVerification->reference_paiement ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Date de paiement :</div>
                                <div class="info-value">{{ $dossierOuvert->paiementVerification->date_paiement->format('d/m/Y H:i') ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Statut du dossier -->
                <div class="section">
                    <div class="section-title">Statut du Dossier</div>
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">Statut :</div>
                            <div class="info-value">
                                <span class="status-badge">{{ $dossierOuvert->statut_formate }}</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Date d'ouverture :</div>
                            <div class="info-value">{{ $dossierOuvert->date_ouverture->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($dossierOuvert->statut === 'finalise')
                        <div class="info-row">
                            <div class="info-label">Date de finalisation :</div>
                            <div class="info-value">{{ now()->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                        <div class="info-row">
                            <div class="info-label">Géré par :</div>
                            <div class="info-value">
                                @if($dossierOuvert->agent)
                                    {{ $dossierOuvert->agent->nom ?? ($dossierOuvert->agent->email ?? 'Agent') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Montant total en pleine largeur -->
        @if($dossierOuvert->paiementVerification)
        <div class="amount-box">
            <div class="amount-label">Montant Total</div>
            <div class="amount-value">{{ number_format($dossierOuvert->paiementVerification->montant_paye, 0, ',', ' ') }} FCFA</div>
        </div>
        @endif
        
        <!-- Notes -->
        @if($dossierOuvert->notes)
        <div class="section">
            <div class="section-title">Notes</div>
            <div style="padding: 8px; background-color: #FEF3C7; border-radius: 3px; font-size: 9px;">
                {{ $dossierOuvert->notes }}
            </div>
        </div>
        @endif
        
        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    Agent MAYELIA
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Client
                </div>
            </div>
        </div>
        
        <!-- Pied de page -->
        <div class="footer">
            <p><strong>MAYELIA MOBILITÉ</strong></p>
            <p>Solutions de mobilité et d'identification</p>
            <p>Ce document est généré automatiquement et certifie le traitement du dossier.</p>
            <p style="margin-top: 10px;">Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>

