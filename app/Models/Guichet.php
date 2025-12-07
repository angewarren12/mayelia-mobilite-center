<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guichet extends Model
{
    protected $fillable = [
        'nom',
        'centre_id',
        'user_id',
        'statut',
        'type_services'
    ];

    protected $casts = [
        'type_services' => 'array'
    ];

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
