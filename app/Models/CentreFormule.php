<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreFormule extends Model
{
    use HasFactory;

    protected $table = 'centre_formules';

    protected $fillable = [
        'centre_id',
        'formule_id',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // Relations
    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function formule()
    {
        return $this->belongsTo(Formule::class);
    }
}