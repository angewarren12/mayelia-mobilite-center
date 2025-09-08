<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourTravail extends Model
{
    use HasFactory;

    protected $table = 'jours_travail';

    protected $fillable = [
        'centre_id',
        'jour_semaine',
        'actif',
        'heure_debut',
        'heure_fin',
        'pause_debut',
        'pause_fin',
        'intervalle_minutes'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'pause_debut' => 'datetime:H:i',
        'pause_fin' => 'datetime:H:i',
    ];

    /**
     * Relation avec le centre
     */
    public function centre()
    {
        return $this->belongsTo(Centre::class);
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
