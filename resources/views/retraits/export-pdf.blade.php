<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Retraits de Carte</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #e11d48; padding-bottom: 10px; }
        .header h1 { color: #e11d48; margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        .meta { margin-bottom: 20px; width: 100%; }
        .meta td { vertical-align: top; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 8px; border: 1px solid #e5e7eb; }
        .table td { padding: 8px; border: 1px solid #e5e7eb; }
        .status { padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .status-termine { background-color: #dcfce7; color: #166534; }
        .status-cours { background-color: #dbeafe; color: #1e40af; }
        .status-attente { background-color: #f3f4f6; color: #4b5563; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #9ca3af; }
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
                    <h1 style="color: #02913F;">ONECI</h1>
                    <p style="margin: 0; font-weight: bold; color: #333;">Rapport des retraits de carte</p>
                </td>
                <td style="text-align: right; border: none; width: 30%;">
                    <!-- Espace vide pour équilibre -->
                </td>
            </tr>
        </table>
    </div>

    <table class="meta">
        <tr>
            <td>
                <strong>Date du rapport :</strong> {{ $date_export }}<br>
                <strong>Généré par :</strong> {{ Auth::user()->nom_complet }}
            </td>
            <td style="text-align: right;">
                @if($filters['start'] || $filters['end'])
                    <strong>Période :</strong> 
                    {{ $filters['start'] ? \Carbon\Carbon::parse($filters['start'])->format('d/m/Y') : 'Début' }}
                    au
                    {{ $filters['end'] ? \Carbon\Carbon::parse($filters['end'])->format('d/m/Y') : 'Fin' }}
                @else
                    <strong>Période :</strong> Historique complet
                @endif
                @if($filters['statut'])
                    <br><strong>Statut :</strong> {{ ucfirst($filters['statut']) }}
                @endif
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>N° Récépissé</th>
                <th>Client</th>
                <th>Téléphone</th>
                <th>Type de Carte</th>
                <th>Date Arrivée</th>
                <th>Statut</th>
                <th>Terminé le</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->retraitCarte->numero_recepisse ?? '---' }}</td>
                <td>{{ $ticket->retraitCarte->client->nom_complet ?? 'N/A' }}</td>
                <td>{{ $ticket->retraitCarte->client->telephone ?? 'N/A' }}</td>
                <td>{{ $ticket->retraitCarte->type_piece ?? 'CNI' }}</td>
                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="status status-{{ $ticket->statut == 'terminé' ? 'termine' : ($ticket->statut == 'en_cours' ? 'cours' : 'attente') }}">
                        {{ $ticket->statut }}
                    </span>
                </td>
                <td>{{ $ticket->completed_at ? $ticket->completed_at->format('d/m/Y H:i') : '---' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Page 1/1 - Document généré par Mayelia QMS
    </div>
</body>
</html>
