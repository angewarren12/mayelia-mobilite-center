<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequis extends Model
{
    protected $fillable = [
        'service_id',
        'type_demande',
        'nom_document',
        'description',
        'obligatoire',
        'ordre'
    ];

    protected $casts = [
        'obligatoire' => 'boolean'
    ];

    /**
     * Relation avec le service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope pour un service et type de demande
     */
    public function scopePourService($query, $serviceId, $typeDemande)
    {
        return $query->where('service_id', $serviceId)
                    ->where('type_demande', $typeDemande)
                    ->orderBy('ordre');
    }

    /**
     * Scope pour les documents obligatoires
     */
    public function scopeObligatoires($query)
    {
        return $query->where('obligatoire', true);
    }
}
