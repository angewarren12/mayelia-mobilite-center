<?php

/**
 * Script de Déploiement Simplifié
 * 
 * Permet d'exécuter des commandes de maintenance après un déploiement FTP.
 * Sécurisé par un TOKEN.
 * 
 * Utilisation: https://votre-site.com/deploy.php?token=VOTRE_TOKEN_SECRET&action=migrate
 */

// ⚠️ CHANGEZ CE TOKEN IMMÉDIATEMENT !!!
// Mettez une longue chaîne aléatoire ici
define('DEPLOY_TOKEN', 'CHANGE_ME_' . md5(date('Y-m-d')));

// Configuration
define('LARAVEL_PATH', __DIR__ . '/../laravel-app'); // Chemin vers le dossier laravel

// Vérification du token
$inputToken = $_GET['token'] ?? '';
if ($inputToken !== DEPLOY_TOKEN) {
    http_response_code(403);
    die('ACCESS DENIED: Invalid Token');
}

// Fonction pour exécuter une commande artisan
function runArtisan($command) {
    $output = [];
    $status = 0;
    
    // On essaie d'abord via exec si disponible
    if (function_exists('exec')) {
        $cmd = 'cd ' . escapeshellarg(LARAVEL_PATH) . ' && php artisan ' . $command . ' --force 2>&1';
        exec($cmd, $output, $status);
    } else {
        $output[] = "La fonction 'exec' est désactivée sur ce serveur.";
    }
    
    echo "<h3>Commande: <code>php artisan $command</code></h3>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
    echo "<hr>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Déploiement Mayelia</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; background: #f0f0f0; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #11B49A; }
        pre { background: #333; color: #fff; padding: 10px; overflow-x: auto; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛠 Maintenance Après Déploiement</h1>
        
        <?php
        $action = $_GET['action'] ?? 'status';
        
        if ($action === 'migrate') {
            runArtisan('migrate');
        } elseif ($action === 'cache') {
            runArtisan('config:clear');
            runArtisan('cache:clear');
            runArtisan('view:clear');
            runArtisan('route:clear');
            
            runArtisan('config:cache');
            runArtisan('view:cache');
            runArtisan('route:cache');
            echo "<p style='color:green'>✅ Cache régénéré !</p>";
        } elseif ($action === 'link') {
            runArtisan('storage:link');
        } else {
            echo "<p>Actions disponibles :</p>";
            echo "<ul>";
            echo "<li><a href='?token=" . DEPLOY_TOKEN . "&action=cache'>Vider et régénerer le cache</a> (Recommandé après chaque déploiement)</li>";
            echo "<li><a href='?token=" . DEPLOY_TOKEN . "&action=migrate'>Lancer les migrations</a> (Seulement si modif DB)</li>";
            echo "<li><a href='?token=" . DEPLOY_TOKEN . "&action=link'>Créer le lien Storage</a> (Une seule fois)</li>";
            echo "</ul>";
        }
        ?>
        
        <p><a href="/">Retour au site</a></p>
    </div>
</body>
</html>
