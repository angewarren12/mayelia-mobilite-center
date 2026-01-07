# 🚀 Guide de Déploiement - Correction Erreur 403 QMS API

## 📋 Résumé de la Correction

**Problème :** Erreur 403 sur `/qms/api/queue/{centre}` pour les requêtes publiques (TV d'affichage)

**Solution :** Modification de `getQueueData()` pour permettre l'accès public tout en conservant la sécurité pour les utilisateurs authentifiés

## ✅ Fichiers Modifiés

1. **`app/Http/Controllers/QmsController.php`**
   - Ligne 391-402 : Modification de `getQueueData()` pour vérifier `Auth::check()` avant d'appeler `canAccessCentre()`

## 🔧 Étapes de Déploiement

### 1. Vérifier le Code Modifié

Assurez-vous que le fichier `app/Http/Controllers/QmsController.php` contient bien cette modification :

```php
public function getQueueData($centreId)
{
    // Vérifier que le centre existe
    $centre = Centre::findOrFail($centreId);
    
    // Sécurité : Vérifier l'accès au centre uniquement si l'utilisateur est authentifié
    // Si pas authentifié (TV publique), permettre l'accès public
    if (Auth::check()) {
        if (!Auth::user()->canAccessCentre($centreId)) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
    }
    // ... reste du code
}
```

### 2. Déployer le Code

**Option A : Via Git (Recommandé)**
```bash
# Sur le serveur de production
cd /chemin/vers/mayelia-mobilite-center
git pull origin main  # ou la branche appropriée
```

**Option B : Via FTP/SFTP**
- Télécharger le fichier modifié `app/Http/Controllers/QmsController.php`
- Remplacer le fichier sur le serveur

### 3. Vider le Cache Laravel

**Sur Windows (PowerShell) :**
```powershell
cd C:\chemin\vers\mayelia-mobilite-center
.\clear-cache-qms.ps1
```

**Sur Linux/Mac :**
```bash
cd /chemin/vers/mayelia-mobilite-center
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear
```

**Via SSH sur le serveur de production :**
```bash
ssh user@rendez-vous.mayeliamobilite.com
cd /var/www/mayelia-mobilite-center  # Ajuster le chemin
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear
```

### 4. Vérifier les Permissions

Assurez-vous que les fichiers ont les bonnes permissions :
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Ajuster selon votre serveur
```

### 5. Tester l'API

**Test depuis le navigateur :**
```
https://rendez-vous.mayeliamobilite.com/qms/api/queue/1
```

**Test depuis la ligne de commande :**
```bash
curl https://rendez-vous.mayeliamobilite.com/qms/api/queue/1
```

**Résultat attendu :**
```json
{
  "last_called": {...},
  "active_tickets": [...],
  "history": [...],
  "waiting": [...],
  "waiting_count": 0,
  "tv_status": {...}
}
```

**Pas d'erreur 403 !**

### 6. Vérifier la TV d'Affichage

1. Ouvrir : `https://rendez-vous.mayeliamobilite.com/qms/display/1`
2. Ouvrir la console du navigateur (F12)
3. Vérifier dans l'onglet Network :
   - La requête `/qms/api/queue/1` retourne **200 OK**
   - Les données JSON sont bien reçues
   - Plus d'erreur 403

## 🔍 Vérification Post-Déploiement

### Checklist

- [ ] Le code modifié est déployé sur le serveur
- [ ] Le cache Laravel a été vidé
- [ ] L'API `/qms/api/queue/1` retourne 200 OK (pas 403)
- [ ] La TV d'affichage charge les données correctement
- [ ] Les agents authentifiés peuvent toujours accéder à l'API
- [ ] Les logs ne montrent plus d'erreurs 403

### Logs à Vérifier

```bash
# Vérifier les logs Laravel
tail -f storage/logs/laravel.log

# Vérifier les logs du serveur web (Nginx/Apache)
tail -f /var/log/nginx/error.log  # Nginx
tail -f /var/log/apache2/error.log  # Apache
```

## 🐛 Dépannage

### Si l'erreur 403 persiste :

1. **Vérifier que le code est bien déployé**
   ```bash
   grep -A 5 "if (Auth::check())" app/Http/Controllers/QmsController.php
   ```
   Doit afficher la nouvelle vérification.

2. **Vérifier que le cache est bien vidé**
   ```bash
   php artisan route:list | grep "qms/api/queue"
   ```
   Doit afficher la route.

3. **Vérifier les middlewares globaux**
   - Vérifier `bootstrap/app.php` pour voir s'il y a des middlewares qui bloquent
   - Vérifier `.htaccess` ou la configuration Nginx/Apache

4. **Vérifier les logs**
   ```bash
   tail -n 50 storage/logs/laravel.log | grep "403\|Forbidden\|getQueueData"
   ```

5. **Tester directement la méthode**
   ```bash
   php artisan tinker
   >>> $controller = new App\Http\Controllers\QmsController(...);
   >>> $controller->getQueueData(1);
   ```

### Si d'autres erreurs apparaissent :

- **Erreur 500** : Vérifier les logs Laravel pour voir l'erreur exacte
- **Erreur 404** : Vérifier que les routes sont bien enregistrées (`php artisan route:list`)
- **Timeout** : Vérifier la configuration PHP (max_execution_time, memory_limit)

## 📝 Notes Importantes

1. **Cache Laravel** : Le cache DOIT être vidé après chaque modification de contrôleur/routes
2. **Permissions** : Les fichiers doivent avoir les bonnes permissions pour que Laravel puisse écrire dans `storage/`
3. **Environnement** : Vérifier que vous êtes sur le bon environnement (production, pas local)
4. **Sauvegarde** : Toujours faire une sauvegarde avant de déployer

## ✅ Validation Finale

Après déploiement, la TV d'affichage doit :
- ✅ Charger les données sans erreur 403
- ✅ Afficher les tickets actifs
- ✅ Afficher l'historique
- ✅ Jouer les annonces vocales correctement
- ✅ Fonctionner sans authentification

## 📞 Support

Si le problème persiste après avoir suivi ce guide :
1. Vérifier les logs Laravel
2. Vérifier la configuration du serveur web
3. Vérifier que le code est bien déployé
4. Contacter l'équipe de développement avec les logs d'erreur

