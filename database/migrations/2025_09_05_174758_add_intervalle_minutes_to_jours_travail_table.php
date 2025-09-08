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
        Schema::table('jours_travail', function (Blueprint $table) {
            $table->integer('intervalle_minutes')->default(60)->after('pause_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jours_travail', function (Blueprint $table) {
            $table->dropColumn('intervalle_minutes');
        });
    }
};