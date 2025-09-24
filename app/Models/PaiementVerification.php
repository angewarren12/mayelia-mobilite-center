<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementVerification extends Model
{
    use HasFactory;

    protected $table = 'paiement_verification';

    protected $fillable = [
        'dossier_ouvert_id',
        'montant_paye',
        'date_paiement',
        'mode_paiement',
        'reference_paiement',
        'recu_tracabilite_path',
        'verifie_par',
        'date_verification'
    ];

    protected $casts = [
        'montant_paye' => 'decimal:2',
        'date_paiement' => 'datetime',
        'date_verification' => 'datetime',
    ];

    /**
     * Relation avec le dossier ouvert
     */
    public function dossierOuvert()
    {
        return $this->belongsTo(DossierOuvert::class);
    }

    /**
     * Relation avec l'agent qui a vérifié
     */
    public function verifiePar()
    {
        return $this->belongsTo(User::class, 'verifie_par');
    }

    /**
     * Obtenir le montant formaté
     */
    public function getMontantFormateAttribute()
    {
        return number_format($this->montant_paye, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir l'URL du reçu de traçabilité
     */
    public function getRecuTracabiliteUrlAttribute()
    {
        return $this->recu_tracabilite_path ? asset('storage/' . $this->recu_tracabilite_path) : null;
    }
}