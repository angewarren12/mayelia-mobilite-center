<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Agent QMS</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 5px; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic Agent QMS</h1>
    
    <div class="section">
        <h2>1. Utilisateur connecté</h2>
        <pre>{{ json_encode(Auth::user()->only(['id', 'nom', 'prenom', 'email', 'role', 'centre_id', 'statut']), JSON_PRETTY_PRINT) }}</pre>
        @if(Auth::user()->is_agent_biometrie)
            <p class="success">✓ Rôle agent_biometrie détecté correctement</p>
        @else
            <p class="warning">⚠ Rôle: {{ Auth::user()->role }}</p>
        @endif
    </div>

    <div class="section">
        <h2>2. Guichet assigné</h2>
        @php
            $user = Auth::user();
            $assignedGuichet = \App\Models\Guichet::where('user_id', $user->id)
                ->with(['centre'])
                ->first();
        @endphp
        
        @if($assignedGuichet)
            <p class="success">✓ Guichet trouvé</p>
            <pre>{{ json_encode($assignedGuichet->only(['id', 'nom', 'centre_id', 'user_id', 'statut']), JSON_PRETTY_PRINT) }}</pre>
            
            @if($assignedGuichet->centre)
                <p class="success">✓ Centre associé trouvé</p>
                <pre>Centre: {{ json_encode($assignedGuichet->centre->only(['id', 'nom']), JSON_PRETTY_PRINT) }}</pre>
            @else
                <p class="error">✗ Aucun centre associé au guichet</p>
            @endif
        @else
            <p class="error">✗ Aucun guichet assigné à cet utilisateur</p>
            
            <h3>Tous les guichets disponibles :</h3>
            @php
                $allGuichets = \App\Models\Guichet::with('centre')->get();
            @endphp
            <pre>{{ json_encode($allGuichets->map(fn($g) => [
                'id' => $g->id,
                'nom' => $g->nom,
                'centre_id' => $g->centre_id,
                'user_id' => $g->user_id,
                'centre_nom' => $g->centre->nom ?? 'N/A'
            ]), JSON_PRETTY_PRINT) }}</pre>
        @endif
    </div>

    <div class="section">
        <h2>3. File d'attente biométrie</h2>
        @php
            $ticketsEnAttente = \App\Models\Ticket::where('statut', \App\Models\Ticket::STATUT_EN_ATTENTE_BIOMETRIE)
                ->with('service')
                ->get();
        @endphp
        
        <p>Nombre de tickets en attente biométrie: <strong>{{ $ticketsEnAttente->count() }}</strong></p>
        
        @if($ticketsEnAttente->count() > 0)
            <pre>{{ json_encode($ticketsEnAttente->map(fn($t) => [
                'id' => $t->id,
                'numero' => $t->numero,
                'service' => $t->service->nom ?? 'N/A',
                'centre_id' => $t->centre_id,
                'created_at' => $t->created_at->format('Y-m-d H:i:s')
            ]), JSON_PRETTY_PRINT) }}</pre>
        @else
            <p class="warning">⚠ Aucun ticket en attente biométrie</p>
            <p>Pour tester : Créez un ticket avec un agent normal et terminez-le.</p>
        @endif
    </div>

    <div class="section">
        <h2>4. Test API Queue Data</h2>
        @if($assignedGuichet && $assignedGuichet->centre_id)
            <p>URL API: <code>/qms/api/queue/{{ $assignedGuichet->centre_id }}</code></p>
            <button onclick="testAPI()">Tester l'API</button>
            <pre id="api-result"></pre>
            
            <script>
                function testAPI() {
                    fetch('/qms/api/queue/{{ $assignedGuichet->centre_id }}')
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('api-result').textContent = JSON.stringify(data, null, 2);
                        })
                        .catch(err => {
                            document.getElementById('api-result').textContent = 'ERREUR: ' + err.message;
                        });
                }
            </script>
        @else
            <p class="error">✗ Impossible de tester l'API (pas de centre_id)</p>
        @endif
    </div>

    <div class="section">
        <h2>5. Variables passées au template</h2>
        <p>Variables disponibles dans la vue agent.blade.php :</p>
        <ul>
            <li>$assignedGuichet: {{ isset($assignedGuichet) ? '✓ Défini' : '✗ Non défini' }}</li>
            <li>$centreId: {{ isset($centreId) ? '✓ Défini (' . $centreId . ')' : '✗ Non défini' }}</li>
        </ul>
    </div>

    <div class="section">
        <h2>6. Actions recommandées</h2>
        @if(!$assignedGuichet)
            <p class="error"><strong>PROBLÈME IDENTIFIÉ :</strong> Aucun guichet assigné</p>
            <p><strong>Solution :</strong></p>
            <ol>
                <li>Allez dans Admin > Gestion des Guichets</li>
                <li>Modifiez un guichet existant ou créez-en un nouveau</li>
                <li>Dans le champ "Agent assigné", sélectionnez : <strong>{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</strong></li>
                <li>Enregistrez</li>
            </ol>
        @elseif(!$assignedGuichet->centre_id)
            <p class="error"><strong>PROBLÈME IDENTIFIÉ :</strong> Le guichet n'a pas de centre_id</p>
            <p><strong>Solution :</strong> Modifiez le guichet pour lui assigner un centre</p>
        @elseif($ticketsEnAttente->count() == 0)
            <p class="success"><strong>Configuration OK !</strong></p>
            <p class="warning">⚠ Mais aucun ticket en attente biométrie</p>
            <p><strong>Pour tester le workflow complet :</strong></p>
            <ol>
                <li>Connectez-vous avec un agent normal (pas biométrie)</li>
                <li>Allez sur "Guichet Agent"</li>
                <li>Appelez un ticket</li>
                <li>Cliquez sur "Terminer" → Le ticket passera en "En attente biométrie"</li>
                <li>Reconnectez-vous avec l'agent biométrie</li>
                <li>Vous verrez le ticket dans votre file</li>
            </ol>
        @else
            <p class="success"><strong>✓ Tout semble OK !</strong></p>
            <p>Si l'interface QMS est vide, le problème vient probablement du JavaScript.</p>
            <p>Vérifiez la console du navigateur (F12) pour voir les erreurs.</p>
        @endif
    </div>

    <div class="section">
        <a href="{{ route('qms.agent') }}" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">
            Retour à l'interface QMS
        </a>
    </div>
</body>
</html>
