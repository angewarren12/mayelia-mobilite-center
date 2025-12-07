<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Récupère l'utilisateur authentifié
     * 
     * @return User|null
     */
    public function getAuthenticatedUser()
    {
        return Auth::user();
    }

    /**
     * Vérifie si l'utilisateur connecté est un admin
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return false;
        }

        return $user->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur connecté est un agent
     * 
     * @return bool
     */
    public function isAgent(): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return false;
        }

        return $user->role === 'agent';
    }

    /**
     * Récupère le type d'utilisateur ('admin' ou 'agent')
     * 
     * @return string|null
     */
    public function getUserType(): ?string
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return null;
        }

        return $user->role;
    }

    /**
     * Vérifie si l'utilisateur a une permission
     * Les admins ont toutes les permissions
     * 
     * @param string $module
     * @param string $action
     * @return bool
     */
    public function hasPermission(string $module, string $action): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return false;
        }

        return $user->hasPermission($module, $action);
    }
}
