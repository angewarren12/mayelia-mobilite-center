<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'numero',
        'centre_id',
        'service_id',
        'user_id',
        'guichet_id',
        'statut',
        'type',
        'priorite',
        'heure_rdv',
        'called_at',
        'completed_at',
        'metadata',
        'client_id',
        'retrait_type_piece',
        'retrait_numero_recepisse',
        'retrait_scan_recepisse',
        'retrait_numero_piece_finale',
        'retrait_date_expiration_piece',
    ];

    protected $casts = [
        'heure_rdv' => 'datetime:H:i',
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
        'retrait_date_expiration_piece' => 'date',
    ];

    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_APPELÉ = 'appelé';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINÉ = 'terminé';
    const STATUT_ABSENT = 'absent';
    const STATUT_ANNULÉ = 'annulé';
    const STATUT_EN_ATTENTE_BIOMETRIE = 'en_attente_biometrie';
    const STATUT_EN_COURS_BIOMETRIE = 'en_cours_biometrie';

    // Constantes pour les types
    const TYPE_RDV = 'rdv';
    const TYPE_SANS_RDV = 'sans_rdv';

    /**
     * Scope pour les tickets en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    /**
     * Scope pour les tickets appelés
     */
    public function scopeAppelé($query)
    {
        return $query->where('statut', self::STATUT_APPELÉ);
    }

    /**
     * Scope pour les tickets du jour
     */
    public function scopeDuJour($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope pour un centre donné
     */
    public function scopePourCentre($query, $centreId)
    {
        return $query->where('centre_id', $centreId);
    }

    /**
     * Scope pour ordonner par priorité puis date
     */
    public function scopeParPriorite($query)
    {
        return $query->orderBy('priorite', 'desc')->orderBy('created_at', 'asc');
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guichet()
    {
        return $this->belongsTo(Guichet::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function retraitCarte()
    {
        return $this->hasOne(RetraitCarte::class);
    }
}
