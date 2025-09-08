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
        Schema::create('centre_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained('centres')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            // Contrainte unique pour éviter les doublons
            $table->unique(['centre_id', 'service_id'], 'centre_service_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centre_services');
    }
};