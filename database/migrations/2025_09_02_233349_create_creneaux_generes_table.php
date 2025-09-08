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
        Schema::create('creneaux_generes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('formule_id')->constrained()->onDelete('cascade');
            $table->date('date_creneau');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->integer('capacite_disponible');
            $table->integer('capacite_totale');
            $table->enum('statut', ['disponible', 'reserve', 'ferme'])->default('disponible');
            $table->timestamps();
            
            $table->index(['centre_id', 'date_creneau']);
            $table->index(['service_id', 'formule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creneaux_generes');
    }
};
