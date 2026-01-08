<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Retraits de Carte</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #02913F; padding-bottom: 10px; }
        .header h1 { color: #02913F; margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        .meta { margin-bottom: 20px; width: 100%; }
        .meta td { vertical-align: top; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 8px; border: 1px solid #e5e7eb; }
        .table td { padding: 8px; border: 1px solid #e5e7eb; }
        .status { padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .status-termine { background-color: #dcfce7; color: #166534; }
        .status-cours { background-color: #fef3c7; color: #92400e; }
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
                    <h1 style="color: #02913F;">ONECI - {{ $centre->nom }}</h1>
                    <p style="margin: 0; font-weight: bold; color: #333;">Cahier des Retraits de Carte</p>
                </td>
                <td style="text-align: right; border: none; width: 30%;">
                    <!-- Espace vide -->
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
                <strong>Centre :</strong> {{ $centre->nom }} ({{ $centre->ville->nom }})
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Téléphone</th>
                <th>Type</th>
                <th>Récépissé</th>
                <th>N° Carte Finale</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($retraits as $retrait)
            <tr>
                <td>{{ $retrait->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $retrait->client->nom_complet }}</td>
                <td>{{ $retrait->client->telephone }}</td>
                <td>{{ $retrait->type_piece }}</td>
                <td>{{ $retrait->numero_recepisse }}</td>
                <td>{{ $retrait->numero_piece_finale ?? '---' }}</td>
                <td>
                    <span class="status status-{{ $retrait->statut }}">
                        {{ $retrait->statut == 'termine' ? 'Remis' : 'En attente' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document généré par Mayelia QMS - Page 1/1
    </div>
</body>
</html>
