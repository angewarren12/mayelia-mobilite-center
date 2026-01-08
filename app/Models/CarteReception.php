<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarteReception extends Model
{
    use HasFactory;

    protected $fillable = [
        'centre_id',
        'type_piece',
        'quantite',
        'date_reception',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'date_reception' => 'date',
    ];

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
