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
        if (!Schema::hasColumn('tickets', 'heure_rdv')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->time('heure_rdv')
                    ->nullable()
                    ->after('type')
                    ->comment('Heure du rendez-vous pour calcul de priorité');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'heure_rdv')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('heure_rdv');
            });
        }
    }
};
