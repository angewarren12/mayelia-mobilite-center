<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreCarteStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'centre_id',
        'type_piece',
        'quantite'
    ];

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }
}
