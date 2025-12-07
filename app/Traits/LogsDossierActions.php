<?php

namespace App\Traits;

use App\Models\DossierActionLog;
use Illuminate\Support\Facades\Auth;

trait LogsDossierActions
{
    /**
     * Enregistrer une action sur le dossier
     *
     * @param string $action Le type d'action (ex: 'ouvert', 'documents_verifies')
     * @param string|null $description Description optionnelle
     * @param array|null $data Données additionnelles
     * @return DossierActionLog
     */
    public function logAction(string $action, ?string $description = null, ?array $data = null)
    {
        return DossierActionLog::create([
            'dossier_ouvert_id' => $this->id,
            'user_id' => Auth::id() ?? 1, // Fallback to ID 1 if system action
            'action' => $action,
            'description' => $description,
            'data' => $data,
        ]);
    }

    /**
     * Relation avec les logs d'actions
     */
    public function actionsLog()
    {
        return $this->hasMany(DossierActionLog::class, 'dossier_ouvert_id')->orderBy('created_at', 'desc');
    }
}
