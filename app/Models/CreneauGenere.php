<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreneauGenere extends Model
{
    use HasFactory;

    protected $table = 'creneaux_generes';

    protected $fillable = [
        'centre_id',
        'service_id',
        'formule_id',
        'date_creneau',
        'heure_debut',
        'heure_fin',
        'capacite_disponible',
        'capacite_totale',
        'statut'
    ];

    protected $casts = [
        'date_creneau' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
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
     * Relation avec les rendez-vous
     */
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'creneau_id');
    }

    /**
     * Accessor pour vérifier si le créneau est disponible
     */
    public function getDisponibleAttribute()
    {
        return $this->capacite_disponible > 0 && $this->statut === 'disponible';
    }

    /**
     * Accessor pour le pourcentage de remplissage
     */
    public function getPourcentageRemplissageAttribute()
    {
        if ($this->capacite_totale == 0) {
            return 0;
        }
        
        return round((($this->capacite_totale - $this->capacite_disponible) / $this->capacite_totale) * 100, 2);
    }
}
