<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetraitCarte extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'client_id',
        'type_piece',
        'numero_recepisse',
        'scan_recepisse',
        'numero_piece_finale',
        'date_expiration_piece',
        'dossier_id'
    ];

    protected $casts = [
        'date_expiration_piece' => 'date',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function dossier()
    {
        return $this->belongsTo(DossierOuvert::class, 'dossier_id');
    }
}
