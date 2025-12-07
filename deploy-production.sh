#!/bin/bash

# Commandes à exécuter sur le serveur de production

# 1. Nettoyer le cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Créer le lien symbolique pour le storage
php artisan storage:link

# 4. Vérifier les permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
