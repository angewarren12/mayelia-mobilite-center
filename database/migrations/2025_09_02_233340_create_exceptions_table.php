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
        Schema::create('exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained()->onDelete('cascade');
            $table->date('date_exception');
            $table->enum('type', ['ferme', 'capacite_reduite', 'horaires_modifies']);
            $table->text('description')->nullable();
            $table->time('heure_debut')->nullable(); // si horaires modifiés
            $table->time('heure_fin')->nullable(); // si horaires modifiés
            $table->time('pause_debut')->nullable(); // si horaires modifiés
            $table->time('pause_fin')->nullable(); // si horaires modifiés
            $table->integer('capacite_reduite')->nullable(); // si applicable
            $table->timestamps();
            
            $table->unique(['centre_id', 'date_exception'], 'exceptions_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exceptions');
    }
};
