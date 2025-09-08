<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'centre_id',
        'nom',
        'email',
        'password',
        'telephone',
        'role',
        'statut'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relation avec le centre
     */
    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }

    /**
     * Accessor pour vérifier si l'utilisateur est admin
     */
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    /**
     * Accessor pour vérifier si l'utilisateur est agent
     */
    public function getIsAgentAttribute()
    {
        return $this->role === 'agent';
    }
}
