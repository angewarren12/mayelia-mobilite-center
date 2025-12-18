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
        
        .statut-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        
        .statut-ouvert { background: #E0F2FE; color: #0369A1; }
        .statut-en_cours { background: #FEF3C7; color: #B45309; }
        .statut-finalise { background: #D1FAE5; color: #047857; }
        .statut-annulé { background: #FEE2E2; color: #B91C1C; }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titre }}</h1>
        <p>Généré le {{ $date_export }}</p>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Nombre total de dossiers:</span>
            <span class="info-value">{{ $dossiers->count() }}</span>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>N° Dossier</th>
                <th>Client</th>
                <th>Service</th>
                <th>Centre</th>
                <th>Date ouverture</th>
                <th>Date RDV</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dossiers as $dossier)
                <tr>
                    <td style="font-family: monospace;">{{ str_pad($dossier->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $dossier->rendezVous->client->nom_complet ?? 'N/A' }}</td>
                    <td>{{ $dossier->rendezVous->service->nom ?? 'N/A' }}</td>
                    <td>{{ $dossier->rendezVous->centre->nom ?? 'N/A' }}</td>
                    <td>{{ $dossier->date_ouverture ? $dossier->date_ouverture->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        @if($dossier->rendezVous)
                            {{ $dossier->rendezVous->date_rendez_vous->format('d/m/Y') }} {{ $dossier->rendezVous->tranche_horaire }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="statut-badge statut-{{ $dossier->statut }}">
                            {{ $dossier->statut_formate }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Mayelia Mobilité Center - Système de gestion des dossiers ONECI</p>
    </div>
</body>
</html>

