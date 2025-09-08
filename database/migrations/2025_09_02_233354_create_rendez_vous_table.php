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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creneau_id')->constrained('creneaux_generes')->onDelete('cascade');
            $table->string('client_nom');
            $table->string('client_email');
            $table->string('client_telephone');
            $table->enum('statut', ['confirme', 'annule', 'completed'])->default('confirme');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
