<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DossierOneciTransfer extends Model
{
    protected $fillable = [
        'centre_id',
        'date_envoi',
        'statut',
        'code_transfert',
        'nombre_dossiers',
        'agent_mayelia_id',
        'agent_oneci_id',
        'date_reception_oneci',
        'date_traitement',
        'date_carte_prete',
        'date_recuperation',
        'notes'
    ];

    protected $casts = [
        'date_envoi' => 'date',
        'date_reception_oneci' => 'datetime',
        'date_traitement' => 'datetime',
        'date_carte_prete' => 'datetime',
        'date_recuperation' => 'datetime',
    ];

    /**
     * Relation avec le centre
     */
    public function centre(): BelongsTo
    {
        return $this->belongsTo(Centre::class);
    }

    /**
     * Relation avec l'agent Mayelia
     */
    public function agentMayelia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_mayelia_id');
    }

    /**
     * Relation avec l'agent ONECI
     */
    public function agentOneci(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_oneci_id');
    }

    /**
     * Relation avec les items du transfert
     */
    public function items(): HasMany
    {
        return $this->hasMany(DossierOneciItem::class, 'transfer_id');
    }

    /**
     * Générer un code de transfert unique
     */
    public static function generateCodeTransfert(): string
    {
        do {
            $code = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('code_transfert', $code)->exists());

        return $code;
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatutFormateAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'envoye' => 'Envoyé',
            'recu_oneci' => 'Reçu à l\'ONECI',
            'traite' => 'Traité',
            'carte_prete' => 'Carte prête',
            'recupere' => 'Récupéré',
            default => ucfirst($this->statut)
        };
    }
}
