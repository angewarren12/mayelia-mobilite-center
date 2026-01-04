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
        // Utilisation de DB::statement pour modifier la colonne ENUM (MySQL)
        // Note: Ajouter 'en_attente_biometrie' et 'en_cours_biometrie'
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets MODIFY COLUMN statut ENUM('en_attente', 'appelé', 'en_cours', 'terminé', 'absent', 'annulé', 'en_attente_biometrie', 'en_cours_biometrie') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ancienne définition
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets MODIFY COLUMN statut ENUM('en_attente', 'appelé', 'en_cours', 'terminé', 'absent', 'annulé') DEFAULT 'en_attente'");
    }
};
