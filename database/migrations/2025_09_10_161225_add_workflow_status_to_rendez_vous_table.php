<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            // Modifier la colonne statut pour inclure les nouveaux statuts du workflow
            $table->enum('statut', [
                'confirme',           // Rendez-vous confirmé
                'dossier_ouvert',     // Dossier ouvert par un agent
                'documents_verifies', // Documents vérifiés
                'documents_manquants', // Documents manquants mais processus continué
                'paiement_effectue',  // Paiement effectué au guichet
                'dossier_oneci',      // Dossier au centre ONECI
                'carte_mayelia',      // Carte au centre Mayelia
                'carte_prete',        // Carte prête à être livrée
                'termine',            // Processus terminé
                'annule'              // Annulé
            ])->default('confirme')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            // Revenir aux anciens statuts
            $table->enum('statut', [
                'confirme',
                'annule',
                'termine'
            ])->default('confirme')->change();
        });
    }
};