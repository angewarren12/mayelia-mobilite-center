# 🔧 Correction Erreur 403 - API QMS Queue

## 🐛 Problème Identifié

**Erreur :** `GET /qms/api/queue/1 403 (Forbidden)`

**Cause :**
La méthode `getQueueData()` dans `QmsController` vérifiait l'authentification même pour les requêtes publiques (TV d'affichage). Quand `Auth::user()` était `null`, l'appel à `canAccessCentre()` causait une erreur et retournait 403.

## ✅ Correction Apportée

### Fichier modifié : `app/Http/Controllers/QmsController.php`

**Avant :**
```php
public function getQueueData($centreId)
{
    // Sécurité : Vérifier l'accès au centre
    if (!Auth::user()->canAccessCentre($centreId)) {
        return response()->json(['error' => 'Non autorisé'], 403);
    }
    // ...
}
```

**Après :**
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
    // ...
}
```

## 🔍 Changements Clés

1. **Vérification de l'authentification** : Utilisation de `Auth::check()` avant d'appeler `Auth::user()`
2. **Accès public autorisé** : Si l'utilisateur n'est pas authentifié, l'accès est autorisé (pour la TV)
3. **Sécurité conservée** : Si l'utilisateur est authentifié, les vérifications de permissions restent actives

## 🚀 Déploiement

### Sur le serveur de production :

1. **Vider le cache Laravel** :
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan optimize:clear
   ```

   Ou utiliser le script PowerShell fourni :
   ```powershell
   .\clear-cache-qms.ps1
   ```

2. **Vérifier que le code est déployé** :
   - Vérifier que le fichier `app/Http/Controllers/QmsController.php` contient bien la modification
   - Ligne 398 doit contenir : `if (Auth::check()) {`

3. **Tester l'API** :
   ```bash
   curl https://rendez-vous.mayeliamobilite.com/qms/api/queue/1
   ```
   
   Devrait retourner du JSON avec les données de la queue (pas d'erreur 403).

## 📋 Routes Concernées

- ✅ `/qms/api/queue/{centre}` - Route publique dans `routes/web.php` (ligne 117)
- ✅ `/api/qms/queue/{centre}` - Route publique dans `routes/api.php` (ligne 25)

Les deux routes appellent la même méthode `getQueueData()` qui est maintenant corrigée.

## ✅ Résultat Attendu

Après correction et vidage du cache :
- ✅ La TV d'affichage peut charger les données sans authentification
- ✅ Les agents authentifiés conservent leurs vérifications de permissions
- ✅ Plus d'erreur 403 pour les requêtes publiques

## 🔍 Vérification

1. Ouvrir la console du navigateur (F12)
2. Aller sur la page TV d'affichage : `/qms/display/1`
3. Vérifier dans l'onglet Network que la requête `/qms/api/queue/1` retourne **200 OK** (pas 403)
4. Vérifier que les données JSON sont bien reçues

## 📝 Notes

- Le cache Laravel doit être vidé après chaque modification de contrôleur/routes
- Si l'erreur persiste après vidage du cache, vérifier :
  - Que le code est bien déployé sur le serveur
  - Qu'il n'y a pas de middleware global qui bloque
  - Les logs Laravel : `storage/logs/laravel.log`

