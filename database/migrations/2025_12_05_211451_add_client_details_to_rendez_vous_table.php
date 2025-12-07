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
            // Réintroduction des colonnes client de base si elles manquent
            if (!Schema::hasColumn('rendez_vous', 'client_nom')) {
                $table->string('client_nom')->nullable();
            }
            if (!Schema::hasColumn('rendez_vous', 'client_email')) {
                $table->string('client_email')->nullable();
            }
            if (!Schema::hasColumn('rendez_vous', 'client_telephone')) {
                $table->string('client_telephone')->nullable();
            }

            // Nouveaux champs détaillés
            if (!Schema::hasColumn('rendez_vous', 'client_prenom')) {
                $table->string('client_prenom')->nullable();
            }
            if (!Schema::hasColumn('rendez_vous', 'date_naissance')) {
                $table->date('date_naissance')->nullable();
            }
            if (!Schema::hasColumn('rendez_vous', 'lieu_naissance')) {
                $table->string('lieu_naissance')->nullable();
            }
            if (!Schema::hasColumn('rendez_vous', 'sexe')) {
                $table->string('sexe', 10)->nullable();
            }
            if (!Schema::hasColumn('rendez_vous', 'adresse')) {
                $table->text('adresse')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
             $columns = [
                 'client_nom', 'client_email', 'client_telephone',
                 'client_prenom', 'date_naissance', 'lieu_naissance', 'sexe', 'adresse'
             ];
             foreach ($columns as $column) {
                 if (Schema::hasColumn('rendez_vous', $column)) {
                     $table->dropColumn($column);
                 }
             }
        });
    }
};
