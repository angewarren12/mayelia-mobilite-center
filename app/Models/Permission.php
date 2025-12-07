<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'module',
        'action',
        'name',
        'description'
    ];

    /**
     * Relation avec les agents
     */
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'agent_permissions')
                    ->withTimestamps();
    }

    /**
     * Scope pour filtrer par module
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope pour filtrer par action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Accessor pour obtenir la clé complète (module.action)
     */
    public function getKeyAttribute(): string
    {
        return $this->module . '.' . $this->action;
    }
}
