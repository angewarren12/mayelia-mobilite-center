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
        'notes'
    ];

    protected $casts = [
        'date_rendez_vous' => 'date',
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

    /**
     * Accessor pour le statut formaté
     */
    public function getStatutFormateAttribute()
    {
        $statuts = [
            'confirme' => 'Confirmé',
            'dossier_ouvert' => 'Dossier ouvert',
            'documents_verifies' => 'Documents vérifiés',
            'documents_manquants' => 'Documents manquants',
            'paiement_effectue' => 'Paiement effectué',
            'dossier_oneci' => 'Dossier ONECI',
            'carte_mayelia' => 'Carte Mayelia',
            'carte_prete' => 'Carte prête',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
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
}












