<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cette migration doit être exécutée EN DERNIER après toutes les migrations de données
     */
    public function up(): void
    {
        // Supprimer la table agents seulement si elle existe et que les données ont été migrées
        Schema::dropIfExists('agents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table agents si nécessaire
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->foreignId('centre_id')->constrained('centres')->onDelete('cascade');
            $table->boolean('actif')->default(true);
            $table->timestamp('derniere_connexion')->nullable();
            $table->timestamps();
        });
    }
};
