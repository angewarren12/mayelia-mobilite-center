<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVerification extends Model
{
    use HasFactory;

    protected $table = 'document_verification';

    protected $fillable = [
        'dossier_ouvert_id',
        'document_requis_id',
        'present',
        'commentaire',
        'verifie_par',
        'date_verification',
        'nom_fichier',
        'chemin_fichier',
        'taille_fichier',
        'type_mime'
    ];

    protected $casts = [
        'present' => 'boolean',
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
     * Relation avec le document requis
     */
    public function documentRequis()
    {
        return $this->belongsTo(DocumentRequis::class);
    }

    /**
     * Relation avec l'agent qui a vérifié
     */
    public function verifiePar()
    {
        return $this->belongsTo(User::class, 'verifie_par');
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatutFormateAttribute()
    {
        return $this->present ? 'Présent' : 'Manquant';
    }

    /**
     * Obtenir la classe CSS pour le statut
     */
    public function getStatutClassAttribute()
    {
        return $this->present ? 'text-green-600' : 'text-red-600';
    }
}