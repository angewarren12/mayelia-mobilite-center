<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentreService extends Model
{
    use HasFactory;

    protected $table = 'centre_services';

    protected $fillable = [
        'centre_id',
        'service_id',
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

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}