<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsDossierActions;

class DossierOuvert extends Model
{
    use HasFactory, LogsDossierActions;

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
        'notes',
        'code_barre',
        'statut_oneci',
        'transfer_id',
        'date_envoi_oneci',
        'date_reception_oneci',
        'date_carte_prete',
        'date_recuperation'
    ];

    protected $casts = [
        'date_ouverture' => 'datetime',
        'fiche_pre_enrolement_verifiee' => 'boolean',
        'documents_verifies' => 'boolean',
        'documents_manquants' => 'boolean',
        'informations_client_verifiees' => 'boolean',
        'paiement_verifie' => 'boolean',
        'date_envoi_oneci' => 'datetime',
        'date_reception_oneci' => 'datetime',
        'date_carte_prete' => 'datetime',
        'date_recuperation' => 'datetime',
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
     * Vérifier si le dossier peut être géré par un utilisateur
     */
    public function canBeManagedBy(User $user)
    {
        return $this->agent_id === $user->id;
    }

    /**
     * Relation avec les vérifications de documents
     */
    public function documentVerifications()
    {
        return $this->hasMany(DocumentVerification::class);
    }

    /**
     * Alias pour documentVerifications (compatibilité)
     */
    public function documents()
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
     * Relation avec le transfert ONECI
     */
    public function oneciTransfer()
    {
        return $this->belongsTo(DossierOneciTransfer::class, 'transfer_id');
    }

    /**
     * Relation avec l'item ONECI
     */
    public function oneciItem()
    {
        return $this->hasOne(DossierOneciItem::class, 'dossier_ouvert_id');
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