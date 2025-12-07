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
        Schema::table('dossier_ouvert', function (Blueprint $table) {
            if (!Schema::hasColumn('dossier_ouvert', 'code_barre')) {
                $table->string('code_barre')->unique()->nullable()->after('statut');
            }
            if (!Schema::hasColumn('dossier_ouvert', 'statut_oneci')) {
                $table->enum('statut_oneci', ['envoye', 'recu', 'traite', 'carte_prete', 'recupere'])->nullable()->after('code_barre');
            }
            if (!Schema::hasColumn('dossier_ouvert', 'transfer_id')) {
                $table->foreignId('transfer_id')->nullable()->constrained('dossier_oneci_transfers')->onDelete('set null')->after('statut_oneci');
            }
            if (!Schema::hasColumn('dossier_ouvert', 'date_envoi_oneci')) {
                $table->timestamp('date_envoi_oneci')->nullable()->after('transfer_id');
            }
            if (!Schema::hasColumn('dossier_ouvert', 'date_reception_oneci')) {
                $table->timestamp('date_reception_oneci')->nullable()->after('date_envoi_oneci');
            }
            if (!Schema::hasColumn('dossier_ouvert', 'date_carte_prete')) {
                $table->timestamp('date_carte_prete')->nullable()->after('date_reception_oneci');
            }
            if (!Schema::hasColumn('dossier_ouvert', 'date_recuperation')) {
                $table->timestamp('date_recuperation')->nullable()->after('date_carte_prete');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dossier_ouvert', function (Blueprint $table) {
            $table->dropForeign(['transfer_id']);
            $table->dropColumn([
                'code_barre',
                'statut_oneci',
                'transfer_id',
                'date_envoi_oneci',
                'date_reception_oneci',
                'date_carte_prete',
                'date_recuperation'
            ]);
        });
    }
};
