<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetraitCarte extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'centre_id',
        'client_id',
        'type_piece',
        'numero_recepisse',
        'scan_recepisse',
        'numero_piece_finale',
        'date_expiration_piece',
        'statut',
        'user_id'
    ];

    protected $casts = [
        'date_expiration_piece' => 'date',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
