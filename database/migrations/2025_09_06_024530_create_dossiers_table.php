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
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rendez_vous_id')->constrained('rendez_vous')->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->enum('statut', ['en_attente', 'en_cours', 'dossier_complet', 'dossier_incomplet', 'valide', 'transmis_oneci'])->default('en_attente');
            $table->text('notes_documents_manquants')->nullable();
            $table->json('documents_verifies')->nullable(); // Liste des documents vérifiés
            $table->boolean('paiement_effectue')->default(false);
            $table->string('reference_paiement')->nullable();
            $table->decimal('montant_paiement', 10, 2)->nullable();
            $table->timestamp('date_paiement')->nullable();
            $table->boolean('biometrie_passee')->default(false);
            $table->timestamp('date_biometrie')->nullable();
            $table->timestamp('date_ouverture')->nullable();
            $table->timestamp('date_validation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
