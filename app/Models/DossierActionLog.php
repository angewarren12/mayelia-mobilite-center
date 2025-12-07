<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierActionLog extends Model
{
    protected $table = 'dossier_actions_log';

    protected $fillable = [
        'dossier_ouvert_id',
        'user_id',
        'action',
        'description',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec le dossier ouvert
     */
    public function dossierOuvert()
    {
        return $this->belongsTo(DossierOuvert::class, 'dossier_ouvert_id');
    }

    /**
     * Relation avec l'utilisateur qui a effectué l'action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir une icône pour le type d'action
     */
    public function getIconAttribute(): string
    {
        return match($this->action) {
            'ouvert' => 'fa-folder-open',
            'changement_statut' => 'fa-exchange-alt',
            'mise_a_jour' => 'fa-edit',
            'fiche_verifiee' => 'fa-clipboard-check',
            'documents_verifies' => 'fa-file-check',
            'documents_incomplets' => 'fa-file-excel',
            'infos_client_verifiees' => 'fa-user-check',
            'infos_client_maj' => 'fa-user-edit',
            'paiement_verifie' => 'fa-credit-card',
            'biometrie_effectuee' => 'fa-fingerprint',
            'finalise' => 'fa-check-circle',
            'envoye_oneci' => 'fa-paper-plane',
            'recu_oneci' => 'fa-inbox',
            'carte_prete' => 'fa-id-card',
            'recupere' => 'fa-hand-holding',
            'rejete' => 'fa-times-circle',
            'commentaire' => 'fa-comment',
            default => 'fa-circle',
        };
    }

    /**
     * Obtenir une couleur pour le type d'action
     */
    public function getColorAttribute(): string
    {
        return match($this->action) {
            'ouvert' => 'mayelia',
            'changement_statut' => 'blue',
            'mise_a_jour' => 'blue',
            'fiche_verifiee' => 'green',
            'documents_verifies' => 'green',
            'documents_incomplets' => 'red',
            'infos_client_verifiees' => 'green',
            'infos_client_maj' => 'blue',
            'paiement_verifie' => 'green',
            'biometrie_effectuee' => 'purple',
            'finalise' => 'mayelia',
            'envoye_oneci' => 'indigo',
            'recu_oneci' => 'purple',
            'carte_prete' => 'green',
            'recupere' => 'green',
            'rejete' => 'red',
            'commentaire' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Obtenir le libellé formaté de l'action
     */
    public function getActionFormattedAttribute(): string
    {
        return match($this->action) {
            'ouvert' => 'Dossier ouvert',
            'documents_verifies' => 'Documents vérifiés',
            'paiement_verifie' => 'Paiement vérifié',
            'biometrie_effectuee' => 'Biométrie effectuée',
            'finalise' => 'Dossier finalisé',
            'envoye_oneci' => 'Envoyé à ONECI',
            'recu_oneci' => 'Reçu par ONECI',
            'carte_prete' => 'Carte prête',
            'recupere' => 'Carte récupérée',
            'rejete' => 'Dossier rejeté',
            'commentaire' => 'Commentaire ajouté',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}
