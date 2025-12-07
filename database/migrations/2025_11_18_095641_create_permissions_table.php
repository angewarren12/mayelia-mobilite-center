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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // ex: 'centres', 'creneaux', 'rendez-vous', 'clients'
            $table->string('action'); // ex: 'view', 'create', 'update', 'delete'
            $table->string('name'); // nom descriptif ex: 'Voir les centres'
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['module', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
