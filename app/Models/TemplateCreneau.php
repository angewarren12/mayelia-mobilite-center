<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateCreneau extends Model
{
    use HasFactory;

    protected $table = 'templates_creneaux';

    protected $fillable = [
        'centre_id',
        'service_id',
        'formule_id',
        'jour_semaine',
        'tranche_horaire',
        'capacite',
        'statut'
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
     * Accessor pour le nom du jour
     */
    public function getNomJourAttribute()
    {
        $jours = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche'
        ];

        return $jours[$this->jour_semaine] ?? 'Inconnu';
    }
}
