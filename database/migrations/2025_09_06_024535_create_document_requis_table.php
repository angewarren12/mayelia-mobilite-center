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
        Schema::create('document_requis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('type_demande'); // 'premiere_demande', 'renouvellement', 'modification', 'duplicata'
            $table->string('nom_document');
            $table->text('description')->nullable();
            $table->boolean('obligatoire')->default(true);
            $table->integer('ordre')->default(0); // Pour l'ordre d'affichage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requis');
    }
};
