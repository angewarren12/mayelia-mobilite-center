<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
