<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'duree_rdv',
        'statut'
    ];

    /**
     * Relation avec les centres (many-to-many)
     */
    public function centres()
    {
        return $this->belongsToMany(Centre::class, 'centre_services')
                    ->withPivot('actif')
                    ->withTimestamps();
    }

    /**
     * Relation avec les formules
     */
    public function formules()
    {
        return $this->hasMany(Formule::class);
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

    /**
     * Relation avec les documents requis
     */
    public function documentsRequis()
    {
        return $this->hasMany(DocumentRequis::class);
    }
}

