<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'profession',
        'sexe',
        'numero_piece_identite',
        'type_piece_identite',
        'notes',
        'actif'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'actif' => 'boolean',
    ];

    /**
     * Relation avec les rendez-vous
     */
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * Accessor pour le sexe formaté
     */
    public function getSexeFormateAttribute()
    {
        return $this->sexe === 'M' ? 'Masculin' : 'Féminin';
    }

    /**
     * Scope pour les clients actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour rechercher par nom ou prénom
     */
    public function scopeRecherche($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
              ->orWhere('prenom', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('telephone', 'like', "%{$term}%");
        });
    }
}
