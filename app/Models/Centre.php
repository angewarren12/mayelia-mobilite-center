<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centre extends Model
{
    use HasFactory;

    protected $fillable = [
        'ville_id',
        'nom',
        'adresse',
        'email',
        'telephone',
        'statut'
    ];

    /**
     * Relation avec la ville
     */
    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    /**
     * Relation avec les utilisateurs (admins et agents)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relation avec les services (many-to-many)
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'centre_services')
                    ->withPivot('actif')
                    ->withTimestamps();
    }

    /**
     * Relation avec les services actifs uniquement
     */
    public function servicesActives()
    {
        return $this->belongsToMany(Service::class, 'centre_services')
                    ->wherePivot('actif', true)
                    ->withPivot('actif')
                    ->withTimestamps();
    }

    /**
     * Relation avec les formules (many-to-many)
     */
    public function formules()
    {
        return $this->belongsToMany(Formule::class, 'centre_formules')
                    ->withPivot('actif')
                    ->withTimestamps();
    }

    /**
     * Relation avec les formules actives uniquement
     */
    public function formulesActives()
    {
        return $this->belongsToMany(Formule::class, 'centre_formules')
                    ->wherePivot('actif', true)
                    ->withPivot('actif')
                    ->withTimestamps();
    }

    /**
     * Relation avec les jours de travail
     */
    public function joursTravail()
    {
        return $this->hasMany(JourTravail::class);
    }

    /**
     * Relation avec les templates de créneaux
     */
    public function templatesCreneaux()
    {
        return $this->hasMany(TemplateCreneau::class);
    }

    /**
     * Relation avec les exceptions
     */
    public function exceptions()
    {
        return $this->hasMany(Exception::class);
    }

    /**
     * Relation avec les créneaux générés
     */
    public function creneauxGeneres()
    {
        return $this->hasMany(CreneauGenere::class);
    }
}

