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
            // D'abord ajouter les colonnes manquantes
            $table->foreignId('centre_id')->nullable()->after('id')->constrained('centres')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->after('centre_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('formule_id')->nullable()->after('service_id')->constrained('formules')->onDelete('cascade');
            $table->date('date_rendez_vous')->nullable()->after('formule_id');
            $table->string('tranche_horaire')->nullable()->after('date_rendez_vous');
            
            // Ajouter la colonne client_id
            $table->foreignId('client_id')->nullable()->after('formule_id')->constrained('clients')->onDelete('cascade');
            
            // Ajouter la colonne notes
            $table->text('notes')->nullable()->after('statut');
            
            // Supprimer les anciennes colonnes client
            $table->dropColumn(['client_nom', 'client_email', 'client_telephone']);
            
            // Supprimer l'ancienne colonne creneau_id
            $table->dropForeign(['creneau_id']);
            $table->dropColumn('creneau_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            // Restaurer l'ancienne colonne creneau_id
            $table->foreignId('creneau_id')->constrained('creneaux_generes')->onDelete('cascade');
            
            // Restaurer les anciennes colonnes client
            $table->string('client_nom');
            $table->string('client_email');
            $table->string('client_telephone');
            
            // Supprimer les nouvelles colonnes
            $table->dropForeign(['centre_id']);
            $table->dropColumn('centre_id');
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->dropForeign(['formule_id']);
            $table->dropColumn('formule_id');
            $table->dropColumn('date_rendez_vous');
            $table->dropColumn('tranche_horaire');
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
            $table->dropColumn('notes');
        });
    }
};
