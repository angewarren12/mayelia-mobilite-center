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
        Schema::create('reprogrammations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->onDelete('cascade');
            $table->foreignId('nouveau_rendez_vous_id')->nullable()->constrained('rendez_vous')->onDelete('set null');
            $table->string('raison'); // 'documents_manquants', 'client_absent', 'autre'
            $table->text('notes')->nullable();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reprogrammations');
    }
};
