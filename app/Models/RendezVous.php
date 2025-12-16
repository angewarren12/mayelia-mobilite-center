<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'centre_id',
        'service_id',
        'formule_id',
        'client_id',
        'date_rendez_vous',
        'tranche_horaire',
        'statut',
        'numero_suivi',
        'notes',
        // Informations Client (Directes)
        'client_nom',
        'client_prenom',
        'client_email',
        'client_telephone',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'adresse',
        // Champs ONECI
        'numero_pre_enrolement',
        'token_verification',
        'statut_oneci',
        'donnees_oneci',
        'verified_at'
    ];

    protected $casts = [
        'date_rendez_vous' => 'date',
        'donnees_oneci' => 'array',
        'verified_at' => 'datetime'
    ];

    /**
     * Relation avec le centre
     */
    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    /**
     * Relation avec le service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relation avec la formule
     */
    public function formule()
    {
        return $this->belongsTo(Formule::class);
    }

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec le dossier ouvert
     */
    public function dossierOuvert()
    {
        return $this->hasOne(DossierOuvert::class);
    }

    // Constantes pour les statuts
    const STATUT_CONFIRME = 'confirme';
    const STATUT_DOSSIER_OUVERT = 'dossier_ouvert';
    const STATUT_DOCUMENTS_VERIFIES = 'documents_verifies';
    const STATUT_DOCUMENTS_MANQUANTS = 'documents_manquants';
    const STATUT_PAIEMENT_EFFECTUE = 'paiement_effectue';
    const STATUT_DOSSIER_ONECI = 'dossier_oneci';
    const STATUT_CARTE_MAYELIA = 'carte_mayelia';
    const STATUT_CARTE_PRETE = 'carte_prete';
    const STATUT_TERMINE = 'termine';
    const STATUT_ANNULE = 'annule';

    /**
     * Scope pour les rendez-vous confirmés
     */
    public function scopeConfirme($query)
    {
        return $query->where('statut', self::STATUT_CONFIRME);
    }

    /**
     * Scope pour les rendez-vous d'une date donnée
     */
    public function scopePourDate($query, $date)
    {
        return $query->whereDate('date_rendez_vous', $date);
    }

    /**
     * Scope pour un centre donné
     */
    public function scopePourCentre($query, $centreId)
    {
        return $query->where('centre_id', $centreId);
    }

    /**
     * Scope pour charger les relations fréquentes
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['centre.ville', 'service', 'formule', 'client']);
    }

    /**
     * Accessor pour le statut formaté
     */
    public function getStatutFormateAttribute()
    {
        $statuts = [
            self::STATUT_CONFIRME => 'Confirmé',
            self::STATUT_DOSSIER_OUVERT => 'Dossier ouvert',
            self::STATUT_DOCUMENTS_VERIFIES => 'Documents vérifiés',
            self::STATUT_DOCUMENTS_MANQUANTS => 'Documents manquants',
            self::STATUT_PAIEMENT_EFFECTUE => 'Paiement effectué',
            self::STATUT_DOSSIER_ONECI => 'Dossier ONECI',
            self::STATUT_CARTE_MAYELIA => 'Carte Mayelia',
            self::STATUT_CARTE_PRETE => 'Carte prête',
            self::STATUT_TERMINE => 'Terminé',
            self::STATUT_ANNULE => 'Annulé'
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Accessor pour vérifier si le RDV est actif
     */
    public function getActifAttribute()
    {
        return $this->statut === 'confirme';
    }

    /**
     * Vérifie si le pré-enrôlement ONECI est validé
     */
    public function isOneciVerified(): bool
    {
        return $this->statut_oneci === 'valide' && $this->verified_at !== null;
    }

    /**
     * Accessor pour le statut ONECI formaté
     */
    public function getStatutOneciFormateAttribute(): ?string
    {
        if (!$this->statut_oneci) {
            return null;
        }

        $statuts = [
            'en_attente' => 'En attente de validation',
            'valide' => 'Validé',
            'rejete' => 'Rejeté'
        ];

        return $statuts[$this->statut_oneci] ?? $this->statut_oneci;
    }
}












