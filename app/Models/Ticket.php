<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'numero',
        'centre_id',
        'service_id',
        'user_id',
        'guichet_id',
        'statut',
        'type',
        'priorite',
        'heure_rdv',
        'called_at',
        'completed_at'
    ];

    protected $casts = [
        'heure_rdv' => 'datetime:H:i',
        'called_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guichet()
    {
        return $this->belongsTo(Guichet::class);
    }
}
