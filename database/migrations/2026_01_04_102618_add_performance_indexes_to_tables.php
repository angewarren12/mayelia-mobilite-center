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
        // Index pour rendez_vous
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->index(['centre_id', 'date_rendez_vous'], 'idx_rdv_centre_date');
            $table->index('statut', 'idx_rdv_statut');
            $table->index('numero_suivi', 'idx_rdv_search');
        });

        // Index pour tickets (QMS)
        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['centre_id', 'statut'], 'idx_ticket_centre_statut');
            $table->index('created_at', 'idx_ticket_created');
        });

        // Index pour dossier_ouvert
        Schema::table('dossier_ouvert', function (Blueprint $table) {
            $table->index(['agent_id', 'date_ouverture'], 'idx_dossier_agent_date');
            $table->index('statut', 'idx_dossier_statut');
        });

        // Index pour dossier_actions_log
        Schema::table('dossier_actions_log', function (Blueprint $table) {
            $table->index(['user_id', 'action', 'created_at'], 'idx_log_user_action_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropIndex('idx_rdv_centre_date');
            $table->dropIndex('idx_rdv_statut');
            $table->dropIndex('idx_rdv_search');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_ticket_centre_statut');
            $table->dropIndex('idx_ticket_created');
        });

        Schema::table('dossier_ouvert', function (Blueprint $table) {
            $table->dropIndex('idx_dossier_agent_date');
            $table->dropIndex('idx_dossier_statut');
        });

        Schema::table('dossier_actions_log', function (Blueprint $table) {
            $table->dropIndex('idx_log_user_action_date');
        });
    }
};
