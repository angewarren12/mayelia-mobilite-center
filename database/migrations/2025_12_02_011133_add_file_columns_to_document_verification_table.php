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
        Schema::table('document_verification', function (Blueprint $table) {
            $table->string('nom_fichier')->nullable()->after('commentaire');
            $table->string('chemin_fichier')->nullable()->after('nom_fichier');
            $table->integer('taille_fichier')->nullable()->after('chemin_fichier');
            $table->string('type_mime')->nullable()->after('taille_fichier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_verification', function (Blueprint $table) {
            $table->dropColumn(['nom_fichier', 'chemin_fichier', 'taille_fichier', 'type_mime']);
        });
    }
};
