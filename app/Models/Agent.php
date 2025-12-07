<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agent extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'centre_id',
        'actif',
        'derniere_connexion'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'derniere_connexion' => 'datetime'
    ];

    /**
     * Relation avec le centre
     */
    public function centre(): BelongsTo
    {
        return $this->belongsTo(Centre::class);
    }

    /**
     * Relation avec les dossiers
     */
    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    /**
     * Relation avec les reprogrammations
     */
    public function reprogrammations(): HasMany
    {
        return $this->hasMany(Reprogrammation::class);
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Scope pour les agents actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour les agents d'un centre
     */
    public function scopeDuCentre($query, $centreId)
    {
        return $query->where('centre_id', $centreId);
    }

    /**
     * Relation avec les permissions
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'agent_permissions')
                    ->withTimestamps();
    }

    /**
     * Vérifie si l'agent a une permission spécifique
     * 
     * @param string $module Le module (ex: 'centres', 'creneaux')
     * @param string $action L'action (ex: 'view', 'create', 'update', 'delete')
     * @return bool
     */
    public function hasPermission(string $module, string $action): bool
    {
        return $this->permissions()
            ->where('module', $module)
            ->where('action', $action)
            ->exists();
    }

    /**
     * Alias pour hasPermission
     * 
     * @param string $permission Format: 'module.action' ou 'module' pour vérifier toutes les actions
     * @return bool
     */
    public function can(string $permission): bool
    {
        if (strpos($permission, '.') !== false) {
            [$module, $action] = explode('.', $permission, 2);
            return $this->hasPermission($module, $action);
        }
        
        // Si seulement le module est fourni, vérifier si l'agent a au moins une permission pour ce module
        return $this->permissions()
            ->where('module', $permission)
            ->exists();
    }
}
