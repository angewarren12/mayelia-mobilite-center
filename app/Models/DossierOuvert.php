<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierOuvert extends Model
{
    use HasFactory;

    protected $table = 'dossier_ouvert';

    protected $fillable = [
        'rendez_vous_id',
        'agent_id',
        'date_ouverture',
        'statut',
        'fiche_pre_enrolement_verifiee',
        'documents_verifies',
        'documents_manquants',
        'informations_client_verifiees',
        'paiement_verifie',
        'notes'
    ];

    protected $casts = [
        'date_ouverture' => 'datetime',
        'fiche_pre_enrolement_verifiee' => 'boolean',
        'documents_verifies' => 'boolean',
        'documents_manquants' => 'boolean',
        'informations_client_verifiees' => 'boolean',
        'paiement_verifie' => 'boolean',
    ];

    /**
     * Relation avec le rendez-vous
     */
    public function rendezVous()
    {
        return $this->belongsTo(RendezVous::class);
    }

    /**
     * Relation avec l'agent qui a ouvert le dossier
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Relation avec les vérifications de documents
     */
    public function documentVerifications()
    {
        return $this->hasMany(DocumentVerification::class);
    }

    /**
     * Relation avec la vérification de paiement
     */
    public function paiementVerification()
    {
        return $this->hasOne(PaiementVerification::class);
    }

    /**
     * Vérifier si le dossier peut être géré par un agent
     */
    public function canBeManagedBy(User $agent)
    {
        return $this->agent_id === $agent->id;
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatutFormateAttribute()
    {
        return match($this->statut) {
            'ouvert' => 'Ouvert',
            'en_cours' => 'En cours',
            'finalise' => 'Finalisé',
            default => ucfirst($this->statut)
        };
    }

    /**
     * Obtenir le pourcentage de progression
     */
    public function getProgressionAttribute()
    {
        $etapes = 0;
        $total = 4; // Fiche + Documents + Informations client + Paiement

        if ($this->fiche_pre_enrolement_verifiee) $etapes++;
        if ($this->documents_verifies) $etapes++;
        if ($this->informations_client_verifiees) $etapes++;
        if ($this->paiement_verifie) $etapes++;

        return round(($etapes / $total) * 100);
    }
}