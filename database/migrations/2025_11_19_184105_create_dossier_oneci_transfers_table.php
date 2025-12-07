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
        if (!Schema::hasTable('dossier_oneci_transfers')) {
            Schema::create('dossier_oneci_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained('centres')->onDelete('cascade');
            $table->date('date_envoi');
            $table->enum('statut', ['en_attente', 'envoye', 'recu_oneci', 'traite', 'carte_prete', 'recupere'])->default('en_attente');
            $table->string('code_transfert')->unique();
            $table->integer('nombre_dossiers')->default(0);
            $table->foreignId('agent_mayelia_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('agent_oneci_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('date_reception_oneci')->nullable();
            $table->timestamp('date_traitement')->nullable();
            $table->timestamp('date_carte_prete')->nullable();
            $table->timestamp('date_recuperation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_oneci_transfers');
    }
};
