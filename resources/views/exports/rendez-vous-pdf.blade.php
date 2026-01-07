<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #3B82F6;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        
        .header p {
            color: #666;
            margin: 0;
        }
        
        .info-section {
            background: #F8FAFC;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #374151;
        }
        
        .info-value {
            color: #6B7280;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        
        th {
            background: #3B82F6;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 11px;
        }
        
        tr:nth-child(even) {
            background: #F9FAFB;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-confirme {
            background: #DCFCE7;
            color: #166534;
        }
        
        .status-annule {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .status-termine {
            background: #DBEAFE;
            color: #1E40AF;
        }
        
        .status-dossier_ouvert {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .status-documents_verifies {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .status-paiement_effectue {
            background: #E0E7FF;
            color: #3730A3;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6B7280;
            font-size: 10px;
            border-top: 1px solid #E5E7EB;
            padding-top: 15px;
        }
        
        .no-data {
            text-align: center;
            color: #6B7280;
            font-style: italic;
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="text-align: left; border: none; width: 30%;">
                    <img src="{{ public_path('img/logo-oneci.jpg') }}" alt="Logo" style="height: 60px;">
                </td>
                <td style="text-align: center; border: none; width: 40%;">
                    <h1 style="color: #3B82F6;">ONECI</h1>
                    <p style="margin: 0; font-weight: bold; color: #333;">{{ $titre }}</p>
                    <p style="margin: 0; font-size: 10px; color: #666;">Exporté le {{ $date_export }}</p>
                </td>
                <td style="text-align: right; border: none; width: 30%;">
                </td>
            </tr>
        </table>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Nombre total de rendez-vous :</span>
            <span class="info-value">{{ $rendezVous->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date d'export :</span>
            <span class="info-value">{{ $date_export }}</span>
        </div>
    </div>
    
    @if($rendezVous->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 20%;">Client</th>
                    <th style="width: 15%;">Email</th>
                    <th style="width: 12%;">Téléphone</th>
                    <th style="width: 15%;">Service</th>
                    <th style="width: 12%;">Formule</th>
                    <th style="width: 10%;">Date RDV</th>
                    <th style="width: 8%;">Heure</th>
                    <th style="width: 10%;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rendezVous as $rdv)
                <tr>
                    <td>{{ $rdv->id }}</td>
                    <td>{{ $rdv->client->nom }} {{ $rdv->client->prenom }}</td>
                    <td>{{ $rdv->client->email }}</td>
                    <td>{{ $rdv->client->telephone ?? 'N/A' }}</td>
                    <td>{{ $rdv->service->nom ?? 'N/A' }}</td>
                    <td>{{ $rdv->formule->nom ?? 'N/A' }}</td>
                    <td>{{ $rdv->date_rendez_vous->format('d/m/Y') }}</td>
                    <td>{{ $rdv->tranche_horaire }}</td>
                    <td>
                        <span class="status-badge status-{{ $rdv->statut }}">
                            {{ ucfirst(str_replace('_', ' ', $rdv->statut)) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            Aucun rendez-vous trouvé pour les critères sélectionnés.
        </div>
    @endif
    
    <div class="footer">
        <p>Généré par Mayelia Mobilité Center - {{ $date_export }}</p>
    </div>
</body>
</html>
