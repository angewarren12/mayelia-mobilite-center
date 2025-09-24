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
        Schema::create('dossier_ouvert', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rendez_vous_id');
            $table->unsignedBigInteger('agent_id');
            $table->datetime('date_ouverture');
            $table->enum('statut', ['ouvert', 'en_cours', 'finalise'])->default('ouvert');
            $table->boolean('fiche_pre_enrolement_verifiee')->default(false);
            $table->boolean('documents_verifies')->default(false);
            $table->boolean('documents_manquants')->default(false); // Nouveau statut pour documents manquants
            $table->boolean('paiement_verifie')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Contrainte unique : un rendez-vous ne peut avoir qu'un seul dossier ouvert
            $table->unique('rendez_vous_id');
            
            // Ajouter les contraintes de clé étrangère après la création de la table
            $table->foreign('rendez_vous_id')->references('id')->on('rendez_vous')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_ouvert');
    }
};