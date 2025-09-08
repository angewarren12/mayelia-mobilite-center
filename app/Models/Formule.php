<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formule extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'nom',
        'prix',
        'couleur',
        'statut'
    ];

    /**
     * Relation avec le service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relation avec les templates de créneaux
     */
    public function templatesCreneaux()
    {
        return $this->hasMany(TemplateCreneau::class);
    }

    /**
     * Relation avec les créneaux générés
     */
    public function creneauxGeneres()
    {
        return $this->hasMany(CreneauGenere::class);
    }
}


