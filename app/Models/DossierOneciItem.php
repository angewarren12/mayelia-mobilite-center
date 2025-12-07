<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DossierOneciItem extends Model
{
    protected $fillable = [
        'transfer_id',
        'dossier_ouvert_id',
        'code_barre',
        'statut',
        'date_reception',
        'date_traitement',
        'date_carte_prete',
        'date_recuperation',
        'agent_oneci_id',
        'agent_mayelia_id'
    ];

    protected $casts = [
        'date_reception' => 'datetime',
        'date_traitement' => 'datetime',
        'date_carte_prete' => 'datetime',
        'date_recuperation' => 'datetime',
    ];

    /**
     * Relation avec le transfert
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(DossierOneciTransfer::class, 'transfer_id');
    }

    /**
     * Relation avec le dossier ouvert
     */
    public function dossierOuvert(): BelongsTo
    {
        return $this->belongsTo(DossierOuvert::class, 'dossier_ouvert_id');
    }

    /**
     * Relation avec l'agent ONECI
     */
    public function agentOneci(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_oneci_id');
    }

    /**
     * Relation avec l'agent Mayelia
     */
    public function agentMayelia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_mayelia_id');
    }

    /**
     * Générer un code-barres unique basé sur le numéro de dossier
     */
    public static function generateCodeBarre(DossierOuvert $dossier): string
    {
        // Utiliser l'ID du dossier comme base
        $baseCode = 'DOS-' . str_pad($dossier->id, 8, '0', STR_PAD_LEFT);
        
        // Vérifier l'unicité
        do {
            $code = $baseCode . '-' . strtoupper(substr(uniqid(), -4));
        } while (self::where('code_barre', $code)->exists());

        return $code;
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatutFormateAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'recu' => 'Reçu',
            'traite' => 'Traité',
            'carte_prete' => 'Carte prête',
            'recupere' => 'Récupéré',
            default => ucfirst($this->statut)
        };
    }
}
