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
            $table->string('fiche_pre_enrolement_path')->nullable()->after('fiche_pre_enrolement_verifiee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dossier_ouvert', function (Blueprint $table) {
            $table->dropColumn('fiche_pre_enrolement_path');
        });
    }
};
