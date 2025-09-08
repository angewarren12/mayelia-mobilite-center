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
        Schema::create('centre_formules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained('centres')->onDelete('cascade');
            $table->foreignId('formule_id')->constrained('formules')->onDelete('cascade');
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            // Contrainte unique pour éviter les doublons
            $table->unique(['centre_id', 'formule_id'], 'centre_formule_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centre_formules');
    }
};