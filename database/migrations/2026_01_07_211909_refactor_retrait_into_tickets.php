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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->string('retrait_type_piece')->nullable();
            $table->string('retrait_numero_recepisse')->nullable();
            $table->string('retrait_scan_recepisse')->nullable();
            $table->string('retrait_numero_piece_finale')->nullable();
            $table->date('retrait_date_expiration_piece')->nullable();
        });

        // Transférer les données existantes
        if (Schema::hasTable('retrait_cartes')) {
            $retraits = DB::table('retrait_cartes')->get();
            foreach ($retraits as $retrait) {
                DB::table('tickets')
                    ->where('id', $retrait->ticket_id)
                    ->update([
                        'client_id' => $retrait->client_id,
                        'retrait_type_piece' => $retrait->type_piece,
                        'retrait_numero_recepisse' => $retrait->numero_recepisse,
                        'retrait_scan_recepisse' => $retrait->scan_recepisse,
                        'retrait_numero_piece_finale' => $retrait->numero_piece_finale,
                        'retrait_date_expiration_piece' => $retrait->date_expiration_piece,
                    ]);
            }
            Schema::dropIfExists('retrait_cartes');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
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
        });

        // Migrer les données vers l'arrière
        $tickets = DB::table('tickets')->whereNotNull('retrait_numero_recepisse')->get();
        foreach ($tickets as $ticket) {
            DB::table('retrait_cartes')->insert([
                'ticket_id' => $ticket->id,
                'client_id' => $ticket->client_id,
                'type_piece' => $ticket->retrait_type_piece ?? 'CNI',
                'numero_recepisse' => $ticket->retrait_numero_recepisse,
                'scan_recepisse' => $ticket->retrait_scan_recepisse,
                'numero_piece_finale' => $ticket->retrait_numero_piece_finale,
                'date_expiration_piece' => $ticket->retrait_date_expiration_piece,
                'created_at' => $ticket->created_at,
                'updated_at' => $ticket->updated_at,
            ]);
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn([
                'client_id',
                'retrait_type_piece',
                'retrait_numero_recepisse',
                'retrait_scan_recepisse',
                'retrait_numero_piece_finale',
                'retrait_date_expiration_piece'
            ]);
        });
    }
};
