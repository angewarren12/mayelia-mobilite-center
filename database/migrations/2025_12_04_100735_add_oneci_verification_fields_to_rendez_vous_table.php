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
            // Numéro de pré-enrôlement ONECI
            if (!Schema::hasColumn('rendez_vous', 'numero_pre_enrolement')) {
                $table->string('numero_pre_enrolement')->nullable();
            }
            
            // Token unique pour sécuriser l'accès via lien ONECI
            if (!Schema::hasColumn('rendez_vous', 'token_verification')) {
                $table->string('token_verification')->nullable()->unique();
            }
            
            // Statut de validation ONECI
            if (!Schema::hasColumn('rendez_vous', 'statut_oneci')) {
                $table->enum('statut_oneci', ['en_attente', 'valide', 'rejete'])->nullable();
            }
            
            // Données complètes reçues de l'API ONECI (JSON)
            if (!Schema::hasColumn('rendez_vous', 'donnees_oneci')) {
                $table->json('donnees_oneci')->nullable();
            }
            
            // Date de vérification du statut
            if (!Schema::hasColumn('rendez_vous', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            
            // Index (on essaie de les créer, si erreur c'est pas grave)
            // $table->index('numero_pre_enrolement');
            // $table->index('token_verification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $columns = [
                'numero_pre_enrolement',
                'token_verification',
                'statut_oneci',
                'donnees_oneci',
                'verified_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('rendez_vous', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
