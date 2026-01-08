<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Recréer la table retrait_cartes avec les colonnes nécessaires
        Schema::create('retrait_cartes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('centre_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->enum('type_piece', ['CNI', 'Résident']);
            $table->string('numero_recepisse');
            $table->string('scan_recepisse')->nullable();
            $table->string('numero_piece_finale')->nullable();
            $table->date('date_expiration_piece')->nullable();
            $table->enum('statut', ['en_cours', 'termine'])->default('en_cours');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Agent qui a fait le retrait
            $table->timestamps();
        });

        // 2. Créer la table des stocks par centre
        Schema::create('centre_carte_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained()->onDelete('cascade');
            $table->enum('type_piece', ['CNI', 'Résident']);
            $table->integer('quantite')->default(0);
            $table->timestamps();

            $table->unique(['centre_id', 'type_piece']);
        });

        // 3. Créer la table des réceptions de cartes (historique)
        Schema::create('carte_receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centre_id')->constrained()->onDelete('cascade');
            $table->enum('type_piece', ['CNI', 'Résident']);
            $table->integer('quantite');
            $table->date('date_reception');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Migrer les données depuis tickets vers retrait_cartes
        $tickets = DB::table('tickets')->whereNotNull('retrait_numero_recepisse')->get();
        foreach ($tickets as $ticket) {
            DB::table('retrait_cartes')->insert([
                'ticket_id' => $ticket->id,
                'centre_id' => $ticket->centre_id,
                'client_id' => $ticket->client_id,
                'type_piece' => $ticket->retrait_type_piece ?? 'CNI',
                'numero_recepisse' => $ticket->retrait_numero_recepisse,
                'scan_recepisse' => $ticket->retrait_scan_recepisse,
                'numero_piece_finale' => $ticket->retrait_numero_piece_finale,
                'date_expiration_piece' => $ticket->retrait_date_expiration_piece,
                'statut' => $ticket->statut === 'terminé' ? 'termine' : 'en_cours',
                'user_id' => $ticket->user_id,
                'created_at' => $ticket->created_at,
                'updated_at' => $ticket->updated_at,
            ]);
        }

        // 5. Nettoyer la table tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'retrait_type_piece',
                'retrait_numero_recepisse',
                'retrait_scan_recepisse',
                'retrait_numero_piece_finale',
                'retrait_date_expiration_piece'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('retrait_type_piece')->nullable();
            $table->string('retrait_numero_recepisse')->nullable();
            $table->string('retrait_scan_recepisse')->nullable();
            $table->string('retrait_numero_piece_finale')->nullable();
            $table->date('retrait_date_expiration_piece')->nullable();
        });

        $retraits = DB::table('retrait_cartes')->get();
        foreach ($retraits as $retrait) {
            if ($retrait->ticket_id) {
                DB::table('tickets')->where('id', $retrait->ticket_id)->update([
                    'retrait_type_piece' => $retrait->type_piece,
                    'retrait_numero_recepisse' => $retrait->numero_recepisse,
                    'retrait_scan_recepisse' => $retrait->scan_recepisse,
                    'retrait_numero_piece_finale' => $retrait->numero_piece_finale,
                    'retrait_date_expiration_piece' => $retrait->date_expiration_piece,
                ]);
            }
        }

        Schema::dropIfExists('carte_receptions');
        Schema::dropIfExists('centre_carte_stocks');
        Schema::dropIfExists('retrait_cartes');
    }
};
