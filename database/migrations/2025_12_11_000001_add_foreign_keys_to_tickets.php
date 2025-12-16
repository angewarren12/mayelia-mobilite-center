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
        Schema::table('tickets', function (Blueprint $table) {
            // Ajouter FK seulement si les tables dépendantes existent
            /*
            if (Schema::hasTable('centres')) {
                try {
                    $table->foreign('centre_id')
                        ->references('id')->on('centres')
                        ->onDelete('cascade');
                } catch (\Exception $e) {
                    // FK peut-être déjà existante, ignorer
                }
            }
            
            if (Schema::hasTable('services')) {
                try {
                    $table->foreign('service_id')
                        ->references('id')->on('services')
                        ->onDelete('set null');
                } catch (\Exception $e) {
                    // Ignorer si déjà existante
                }
            }
            
            if (Schema::hasTable('guichets')) {
                try {
                    $table->foreign('guichet_id')
                        ->references('id')->on('guichets')
                        ->onDelete('set null');
                } catch (\Exception $e) {
                    // Ignorer si déjà existante
                }
            }
            
            if (Schema::hasTable('users')) {
                try {
                    $table->foreign('user_id')
                        ->references('id')->on('users')
                        ->onDelete('set null');
                } catch (\Exception $e) {
                    // Ignorer si déjà existante
                }
            }
            */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Supprimer les FK en mode rollback
            $table->dropForeign(['centre_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['guichet_id']);
            $table->dropForeign(['user_id']);
        });
    }
};
