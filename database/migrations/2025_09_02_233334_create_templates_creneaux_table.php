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
        Schema::create('templates_creneaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('formule_id')->constrained()->onDelete('cascade');
            $table->integer('jour_semaine'); // 1=lundi, 7=dimanche
            $table->string('tranche_horaire'); // ex: "07:00-08:00"
            $table->integer('capacite')->default(1);
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
            
            $table->unique(['centre_id', 'service_id', 'formule_id', 'jour_semaine', 'tranche_horaire'], 'templates_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates_creneaux');
    }
};
