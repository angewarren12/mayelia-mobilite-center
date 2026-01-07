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
        'statut',
        'qms_mode',
        'qms_fenetre_minutes',
        'options_tv',
        'options_scan'
    ];

    // Constantes pour les modes QMS
    const QMS_MODE_FIFO = 'fifo';
    const QMS_MODE_FENETRE = 'fenetre_tolerance';

    // Constantes pour les statuts
    const STATUT_ACTIF = 'actif';
    const STATUT_INACTIF = 'inactif';

    protected $casts = [
        'qms_fenetre_minutes' => 'integer',
        'options_tv' => 'array',
        'options_scan' => 'array'
    ];

    /**
     * Scope pour récupérer uniquement les centres actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

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

    /**
     * Relation avec les guichets (QMS)
     */
    public function guichets()
    {
        return $this->hasMany(Guichet::class);
    }

    /**
     * Relation avec les tickets (QMS)
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

