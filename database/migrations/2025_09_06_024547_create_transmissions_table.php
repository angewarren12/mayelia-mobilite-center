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
        Schema::create('transmissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained('centres')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // L'admin qui a fait la transmission
            $table->json('dossiers_ids'); // Liste des IDs des dossiers transmis
            $table->integer('nombre_dossiers');
            $table->text('notes')->nullable();
            $table->timestamp('date_transmission');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transmissions');
    }
};
