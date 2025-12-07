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
        Schema::create('dossier_actions_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_ouvert_id')->constrained('dossier_ouvert')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // 'ouvert', 'documents_verifies', 'paiement_verifie', etc.
            $table->text('description')->nullable();
            $table->json('data')->nullable(); // Données additionnelles
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index('dossier_ouvert_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_actions_log');
    }
};
