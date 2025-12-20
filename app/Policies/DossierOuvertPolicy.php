<?php

namespace App\Policies;

use App\Models\DossierOuvert;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DossierOuvertPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'agent';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DossierOuvert $dossierOuvert): bool
    {
        // Les admins peuvent voir tous les dossiers de leur centre
        if ($user->role === 'admin') {
            return $dossierOuvert->rendezVous && $dossierOuvert->rendezVous->centre_id === $user->centre_id;
        }

        // Les agents voient leurs propres dossiers
        return $dossierOuvert->agent_id === $user->id;
    }

    /**
     * Determine whether the user can update (manage) the model.
     */
    public function update(User $user, DossierOuvert $dossierOuvert): bool
    {
        // On délègue à la méthode du modèle pour garder la cohérence
        return $dossierOuvert->canBeManagedBy($user);
    }

    /**
     * Determine whether the user can reset (un-reject) the model.
     */
    public function reset(User $user, DossierOuvert $dossierOuvert): bool
    {
        // Seuls les admins du même centre peuvent reset un dossier
        return $user->role === 'admin' && 
               $dossierOuvert->rendezVous && 
               $dossierOuvert->rendezVous->centre_id === $user->centre_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DossierOuvert $dossierOuvert): bool
    {
        // Exemple: Aucun suppression autorisée via l'interface pour l'instant
        return $user->role === 'admin';
    }
}
