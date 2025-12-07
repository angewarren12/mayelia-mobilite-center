<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Transfert {{ $transfer->code_transfert }}</title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2563eb;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24pt;
        }
        .header .subtitle {
            color: #666;
            font-size: 12pt;
            margin-top: 5px;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .dossier-section {
            page-break-inside: avoid;
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #fff;
        }
        .dossier-header {
            background-color: #2563eb;
            color: white;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-radius: 5px 5px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dossier-number {
            font-weight: bold;
            font-size: 14pt;
        }
        .barcode-container {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .barcode-text {
            font-family: monospace;
            font-size: 12pt;
            font-weight: bold;
            margin-top: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .info-item {
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 3px;
        }
        .info-item-label {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }
        .info-item-value {
            font-weight: bold;
            font-size: 11pt;
        }
        .documents-list {
            margin: 10px 0;
        }
        .document-item {
            padding: 8px;
            margin: 5px 0;
            border-radius: 3px;
            font-size: 10pt;
        }
        .document-present {
            background-color: #d4edda;
            border-left: 3px solid #28a745;
        }
        .document-missing {
            background-color: #f8d7da;
            border-left: 3px solid #dc3545;
        }
        .workflow-steps {
            margin: 15px 0;
        }
        .workflow-step {
            padding: 8px;
            margin: 5px 0;
            border-left: 3px solid #28a745;
            background-color: #d4edda;
            font-size: 10pt;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>MAYELIA MOBILITÉ</h1>
        <div class="subtitle">Récapitulatif de Transfert à l'ONECI</div>
    </div>

    <!-- Informations générales du transfert -->
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Code Transfert :</span>
            <span>{{ $transfer->code_transfert }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date d'envoi :</span>
            <span>{{ $transfer->date_envoi->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Centre Mayelia :</span>
            <span>{{ $transfer->centre->nom ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nombre de dossiers :</span>
            <span><strong>{{ $transfer->nombre_dossiers }}</strong></span>
        </div>
        @if($transfer->agentMayelia)
        <div class="info-row">
            <span class="info-label">Agent Mayelia :</span>
            <span>{{ $transfer->agentMayelia->nom_complet }}</span>
        </div>
        @endif
        @if($transfer->notes)
        <div class="info-row">
            <span class="info-label">Notes :</span>
            <span>{{ $transfer->notes }}</span>
        </div>
        @endif
    </div>

    <!-- Liste des dossiers -->
    @foreach($items as $item)
        @php
            $dossier = $item->dossierOuvert;
            $rendezVous = $dossier->rendezVous;
            $client = $rendezVous->client ?? null;
            $service = $rendezVous->service ?? null;
            $formule = $rendezVous->formule ?? null;
        @endphp

        <div class="dossier-section">
            <div class="dossier-header">
                <span class="dossier-number">DOSSIER #{{ $dossier->id }}</span>
                <span style="font-size: 10pt;">{{ $loop->iteration }}/{{ $items->count() }}</span>
            </div>

            <!-- Code-barres -->
            <div class="barcode-container">
                {!! $item->barcode_svg !!}
                <div class="barcode-text">{{ $item->code_barre }}</div>
            </div>

            <!-- Informations client -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-item-label">Client</div>
                    <div class="info-item-value">{{ $client->nom_complet ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Email</div>
                    <div class="info-item-value">{{ $client->email ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">Téléphone</div>
                    <div class="info-item-value">{{ $client->telephone ?? 'N/A' }}</div>
                </div>
                @if($client && $client->date_naissance)
                <div class="info-item">
                    <div class="info-item-label">Date de naissance</div>
                    <div class="info-item-value">{{ \Carbon\Carbon::parse($client->date_naissance)->format('d/m/Y') }}</div>
                </div>
                @endif
                @if($client && $client->numero_piece_identite)
                <div class="info-item">
                    <div class="info-item-label">CNI/Passport</div>
                    <div class="info-item-value">{{ $client->numero_piece_identite }}</div>
                </div>
                @endif
            </div>

            <!-- Service et formule -->
            <div style="margin: 15px 0;">
                <h3 style="font-size: 12pt; margin-bottom: 10px; color: #2563eb;">Service demandé</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item-label">Service</div>
                        <div class="info-item-value">{{ $service->nom ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Formule</div>
                        <div class="info-item-value">{{ $formule->nom ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Documents vérifiés -->
            @php
                $documentVerifications = $dossier->documentVerifications ?? collect();
            @endphp
            @if($documentVerifications->count() > 0)
            <div style="margin: 15px 0;">
                <h3 style="font-size: 12pt; margin-bottom: 10px; color: #2563eb;">Documents vérifiés à Mayelia</h3>
                <div class="documents-list">
                    @foreach($documentVerifications as $docVerif)
                        <div class="document-item {{ $docVerif->present ? 'document-present' : 'document-missing' }}">
                            <strong>{{ $docVerif->documentRequis->nom ?? 'Document' }}</strong>
                            <span style="float: right;">
                                @if($docVerif->present)
                                    ✓ Présent
                                @else
                                    ✗ Manquant
                                @endif
                            </span>
                            @if($docVerif->commentaire)
                                <div style="font-size: 9pt; color: #666; margin-top: 3px;">{{ $docVerif->commentaire }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Workflow effectué -->
            <div style="margin: 15px 0;">
                <h3 style="font-size: 12pt; margin-bottom: 10px; color: #2563eb;">Étapes effectuées à Mayelia</h3>
                <div class="workflow-steps">
                    @if($dossier->fiche_pre_enrolement_verifiee)
                    <div class="workflow-step">✓ Fiche de pré-enrôlement vérifiée</div>
                    @endif
                    @if($dossier->documents_verifies)
                    <div class="workflow-step">✓ Documents vérifiés</div>
                    @endif
                    @if($dossier->informations_client_verifiees)
                    <div class="workflow-step">✓ Informations client vérifiées</div>
                    @endif
                    @if($dossier->paiement_verifie && $dossier->paiementVerification)
                    <div class="workflow-step">
                        ✓ Paiement vérifié - 
                        {{ number_format($dossier->paiementVerification->montant_paye, 0, ',', ' ') }} FCFA
                        ({{ $dossier->paiementVerification->mode_paiement ?? 'N/A' }})
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informations de paiement -->
            @if($dossier->paiementVerification)
            <div style="margin: 15px 0;">
                <h3 style="font-size: 12pt; margin-bottom: 10px; color: #2563eb;">Paiement</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item-label">Montant payé</div>
                        <div class="info-item-value">{{ number_format($dossier->paiementVerification->montant_paye, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">Mode de paiement</div>
                        <div class="info-item-value">{{ $dossier->paiementVerification->mode_paiement ?? 'N/A' }}</div>
                    </div>
                    @if($dossier->paiementVerification->reference_paiement)
                    <div class="info-item">
                        <div class="info-item-label">Référence</div>
                        <div class="info-item-value">{{ $dossier->paiementVerification->reference_paiement }}</div>
                    </div>
                    @endif
                    <div class="info-item">
                        <div class="info-item-label">Date de paiement</div>
                        <div class="info-item-value">{{ $dossier->paiementVerification->date_paiement->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Agent Mayelia -->
            @if($dossier->agent)
            <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 9pt; color: #666;">
                Dossier ouvert par : {{ $dossier->agent->nom_complet }} le {{ $dossier->date_ouverture->format('d/m/Y à H:i') }}
            </div>
            @endif
        </div>
    @endforeach

    <!-- Pied de page -->
    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Mayelia Mobilité - Transfert {{ $transfer->code_transfert }}</p>
        <p>Contact centre : {{ $transfer->centre->telephone ?? 'N/A' }} | {{ $transfer->centre->email ?? 'N/A' }}</p>
    </div>
</body>
</html>


