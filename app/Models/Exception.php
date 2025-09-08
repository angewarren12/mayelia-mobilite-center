<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exception extends Model
{
    use HasFactory;

    protected $fillable = [
        'centre_id',
        'date_exception',
        'type',
        'description',
        'heure_debut',
        'heure_fin',
        'pause_debut',
        'pause_fin',
        'capacite_reduite'
    ];

    protected $casts = [
        'date_exception' => 'date',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'pause_debut' => 'datetime',
        'pause_fin' => 'datetime',
    ];

    /**
     * Relation avec le centre
     */
    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    /**
     * Accessor pour le type formaté
     */
    public function getTypeFormateAttribute()
    {
        $types = [
            'ferme' => 'Centre fermé',
            'capacite_reduite' => 'Capacité réduite',
            'horaires_modifies' => 'Horaires modifiés'
        ];

        return $types[$this->type] ?? $this->type;
    }
}


