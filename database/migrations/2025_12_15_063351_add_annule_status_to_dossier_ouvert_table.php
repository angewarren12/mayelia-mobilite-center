<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum pour ajouter 'annulé'
        DB::statement("ALTER TABLE dossier_ouvert MODIFY COLUMN statut ENUM('ouvert', 'en_cours', 'finalise', 'annulé') DEFAULT 'ouvert'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre l'enum sans 'annulé'
        DB::statement("ALTER TABLE dossier_ouvert MODIFY COLUMN statut ENUM('ouvert', 'en_cours', 'finalise') DEFAULT 'ouvert'");
    }
};
