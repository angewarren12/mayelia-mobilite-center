<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dossier extends Model
{
    protected $fillable = [
        'rendez_vous_id',
        'agent_id',
        'statut',
        'notes_documents_manquants',
        'documents_verifies',
        'paiement_effectue',
        'reference_paiement',
        'montant_paiement',
        'date_paiement',
        'biometrie_passee',
        'date_biometrie',
        'date_ouverture',
        'date_validation'
    ];

    protected $casts = [
        'documents_verifies' => 'array',
        'paiement_effectue' => 'boolean',
        'montant_paiement' => 'decimal:2',
        'date_paiement' => 'datetime',
        'biometrie_passee' => 'boolean',
        'date_biometrie' => 'datetime',
        'date_ouverture' => 'datetime',
        'date_validation' => 'datetime'
    ];

    /**
     * Relation avec le rendez-vous
     */
    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVous::class);
    }

    /**
     * Relation avec l'agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Relation avec les reprogrammations
     */
    public function reprogrammations(): HasMany
    {
        return $this->hasMany(Reprogrammation::class);
    }

    /**
     * Accessor pour le statut formaté
     */
    public function getStatutFormateAttribute(): string
    {
        $statuts = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'dossier_complet' => 'Dossier complet',
            'dossier_incomplet' => 'Dossier incomplet',
            'valide' => 'Validé',
            'transmis_oneci' => 'Transmis à l\'ONECI'
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Scope pour les dossiers d'un agent
     */
    public function scopeDeLAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope pour les dossiers d'un centre
     */
    public function scopeDuCentre($query, $centreId)
    {
        return $query->whereHas('rendezVous.centre', function($q) use ($centreId) {
            $q->where('id', $centreId);
        });
    }

    /**
     * Scope pour les dossiers du jour
     */
    public function scopeDuJour($query)
    {
        return $query->whereHas('rendezVous', function($q) {
            $q->whereDate('date_rendez_vous', today());
        });
    }
}
