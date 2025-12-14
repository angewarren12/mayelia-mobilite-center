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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->foreignId('centre_id')->constrained('centres')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('guichet_id')->nullable()->constrained('guichets')->onDelete('set null');
            $table->enum('statut', ['en_attente', 'appelé', 'en_cours', 'terminé', 'absent', 'annulé'])->default('en_attente');
            $table->enum('type', ['rdv', 'sans_rdv'])->default('sans_rdv');
            $table->integer('priorite')->default(1);
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
