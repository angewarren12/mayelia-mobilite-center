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
        'prenom',
        'email',
        'password',
        'telephone',
        'role',
        'statut',
        'derniere_connexion'
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
            'derniere_connexion' => 'datetime',
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

    /**
     * Accessor pour vérifier si l'utilisateur est agent ONECI
     */
    public function getIsOneciAttribute()
    {
        return $this->role === 'oneci';
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        if ($this->prenom) {
            return $this->prenom . ' ' . $this->nom;
        }
        return $this->nom;
    }

    /**
     * Relation avec les dossiers
     */
    public function dossiers()
    {
        return $this->hasMany(\App\Models\Dossier::class, 'agent_id');
    }

    /**
     * Relation avec les dossiers ouverts
     */
    public function dossiersOuverts()
    {
        return $this->hasMany(\App\Models\DossierOuvert::class, 'agent_id');
    }

    /**
     * Relation avec les permissions
     */
    public function permissions()
    {
        return $this->belongsToMany(\App\Models\Permission::class, 'user_permissions')
                    ->withTimestamps();
    }

    /**
     * Vérifie si l'utilisateur a une permission spécifique
     * Les admins ont toutes les permissions
     * 
     * @param string $module Le module (ex: 'centres', 'creneaux')
     * @param string $action L'action (ex: 'view', 'create', 'update', 'delete')
     * @return bool
     */
    public function hasPermission(string $module, string $action): bool
    {
        // Les admins ont toutes les permissions
        if ($this->role === 'admin') {
            return true;
        }

        // Pour les agents, vérifier les permissions
        if ($this->role === 'agent') {
            return $this->permissions()
                ->where('module', $module)
                ->where('action', $action)
                ->exists();
        }

        return false;
    }

    /**
     * Alias pour hasPermission (renommé pour éviter le conflit avec la méthode can() de Laravel)
     * 
     * @param string $permission Format: 'module.action' ou 'module' pour vérifier toutes les actions
     * @return bool
     */
    public function hasPermissionTo(string $permission): bool
    {
        if (strpos($permission, '.') !== false) {
            [$module, $action] = explode('.', $permission, 2);
            return $this->hasPermission($module, $action);
        }
        
        // Si seulement le module est fourni, vérifier si l'utilisateur a au moins une permission pour ce module
        if ($this->role === 'admin') {
            return true;
        }

        return $this->permissions()
            ->where('module', $permission)
            ->exists();
    }

    /**
     * Scope pour les agents actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope pour les agents d'un centre
     */
    public function scopeDuCentre($query, $centreId)
    {
        return $query->where('centre_id', $centreId);
    }

    /**
     * Scope pour les agents
     */
    public function scopeAgents($query)
    {
        return $query->where('role', 'agent');
    }

    public function scopeOneci($query)
    {
        return $query->where('role', 'oneci');
    }

    /**
     * Relation avec le guichet (QMS)
     * L'agent peut être assigné à un guichet
     */
    public function guichet()
    {
        return $this->hasOne(Guichet::class);
    }
}
