<?php

namespace App\Http\Controllers\Concerns;

use App\Services\AuthService;

trait ChecksPermissions
{
    protected $authService;

    /**
     * Initialiser le service d'authentification
     */
    protected function initAuthService()
    {
        if (!$this->authService) {
            $this->authService = app(AuthService::class);
        }
    }

    /**
     * Vérifie si l'utilisateur a une permission et abort si refusé
     * 
     * @param string $module
     * @param string $action
     * @return void
     */
    protected function checkPermission(string $module, string $action): void
    {
        $this->initAuthService();

        // Les admins ont toutes les permissions
        if ($this->authService->isAdmin()) {
            return;
        }

        // Vérifier la permission pour les agents
        if (!$this->authService->hasPermission($module, $action)) {
            abort(403, 'Vous n\'avez pas la permission d\'effectuer cette action.');
        }
    }

    /**
     * Alias pour checkPermission
     * 
     * @param string $module
     * @param string $action
     * @return void
     */
    protected function requirePermission(string $module, string $action): void
    {
        $this->checkPermission($module, $action);
    }

    /**
     * Vérifie si l'utilisateur peut accéder à une ressource (retourne un booléen)
     * 
     * @param string $module
     * @param string $action
     * @return bool
     */
    protected function canAccess(string $module, string $action): bool
    {
        $this->initAuthService();
        return $this->authService->hasPermission($module, $action);
    }
}



