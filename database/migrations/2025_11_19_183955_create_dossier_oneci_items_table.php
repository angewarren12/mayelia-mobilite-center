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
        if (!Schema::hasTable('dossier_oneci_items')) {
            Schema::create('dossier_oneci_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('dossier_oneci_transfers')->onDelete('cascade');
            $table->foreignId('dossier_ouvert_id')->constrained('dossier_ouvert')->onDelete('cascade');
            $table->string('code_barre')->unique();
            $table->enum('statut', ['en_attente', 'recu', 'traite', 'carte_prete', 'recupere'])->default('en_attente');
            $table->timestamp('date_reception')->nullable();
            $table->timestamp('date_traitement')->nullable();
            $table->timestamp('date_carte_prete')->nullable();
            $table->timestamp('date_recuperation')->nullable();
            $table->foreignId('agent_oneci_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('agent_mayelia_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_oneci_items');
    }
};
