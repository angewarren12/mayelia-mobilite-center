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
        Schema::create('retrait_cartes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type_piece', ['CNI', 'Résident']);
            $table->string('numero_recepisse');
            $table->string('scan_recepisse')->nullable();
            $table->string('numero_piece_finale')->nullable();
            $table->date('date_expiration_piece')->nullable();
            $table->unsignedBigInteger('dossier_id')->nullable();
            $table->timestamps();

            $table->foreign('dossier_id')->references('id')->on('dossier_ouvert')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retrait_cartes');
    }
};
