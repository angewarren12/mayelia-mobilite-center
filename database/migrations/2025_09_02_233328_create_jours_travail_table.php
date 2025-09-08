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
        Schema::create('jours_travail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained()->onDelete('cascade');
            $table->integer('jour_semaine'); // 1=lundi, 7=dimanche
            $table->boolean('actif')->default(false);
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->time('pause_debut')->nullable();
            $table->time('pause_fin')->nullable();
            $table->timestamps();
            
            $table->unique(['centre_id', 'jour_semaine'], 'jours_travail_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jours_travail');
    }
};
