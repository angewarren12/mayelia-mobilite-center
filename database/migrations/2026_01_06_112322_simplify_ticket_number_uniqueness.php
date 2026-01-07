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
            // Supprimer l'index unique s'il existe
            if (Schema::hasIndex('tickets', 'tickets_numero_unique')) {
                $table->dropUnique('tickets_numero_unique');
            }
            
            // S'assurer qu'un index simple existe pour la performance
            if (!Schema::hasIndex('tickets', 'tickets_numero_index') && !Schema::hasIndex('tickets', 'numero')) {
                $table->index('numero');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['numero']);
            $table->unique('numero');
        });
    }
};
