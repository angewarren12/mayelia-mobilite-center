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
        Schema::create('paiement_verification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_ouvert_id')->constrained('dossier_ouvert')->onDelete('cascade');
            $table->decimal('montant_paye', 10, 2);
            $table->datetime('date_paiement');
            $table->string('mode_paiement', 100)->nullable();
            $table->string('reference_paiement', 100)->nullable();
            $table->string('recu_tracabilite_path', 500)->nullable();
            $table->foreignId('verifie_par')->constrained('users')->onDelete('cascade');
            $table->datetime('date_verification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiement_verification');
    }
};