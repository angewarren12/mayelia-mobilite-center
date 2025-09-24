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
        Schema::create('document_verification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_ouvert_id')->constrained('dossier_ouvert')->onDelete('cascade');
            $table->foreignId('document_requis_id')->constrained('document_requis')->onDelete('cascade');
            $table->boolean('present')->default(false);
            $table->text('commentaire')->nullable();
            $table->foreignId('verifie_par')->constrained('users')->onDelete('cascade');
            $table->datetime('date_verification')->nullable();
            $table->timestamps();

            // Contrainte unique : un document ne peut être vérifié qu'une fois par dossier
            $table->unique(['dossier_ouvert_id', 'document_requis_id'], 'doc_verif_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_verification');
    }
};