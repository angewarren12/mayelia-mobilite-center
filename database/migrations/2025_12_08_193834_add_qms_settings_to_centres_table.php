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
        Schema::table('centres', function (Blueprint $table) {
            $table->enum('qms_mode', ['fifo', 'fenetre_tolerance'])
                ->default('fifo')
                ->after('statut')
                ->comment('Mode de gestion de file d\'attente');
            
            $table->integer('qms_fenetre_minutes')
                ->default(15)
                ->after('qms_mode')
                ->comment('Fenêtre de tolérance en minutes pour les RDV');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('centres', function (Blueprint $table) {
            $table->dropColumn(['qms_mode', 'qms_fenetre_minutes']);
        });
    }
};
