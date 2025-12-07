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
        // Mettre à jour les clés étrangères qui pointent vers agents pour pointer vers users
        // Note: dossier_ouvert.agent_id pointe déjà vers users, donc pas besoin de le modifier
        
        // Mettre à jour reprogrammations.agent_id si elle existe
        if (Schema::hasTable('reprogrammations')) {
            $hasAgentId = Schema::hasColumn('reprogrammations', 'agent_id');
            if ($hasAgentId) {
                // Supprimer l'ancienne clé étrangère
                try {
                    Schema::table('reprogrammations', function (Blueprint $table) {
                        $table->dropForeign(['agent_id']);
                    });
                } catch (\Exception $e) {
                    // Ignorer si la clé n'existe pas
                }
                
                // Mettre à jour les données : remplacer agent_id par user_id correspondant
                $reprogrammations = DB::table('reprogrammations')->whereNotNull('agent_id')->get();
                foreach ($reprogrammations as $reprog) {
                    $agent = DB::table('agents')->where('id', $reprog->agent_id)->first();
                    if ($agent) {
                        $user = DB::table('users')->where('email', $agent->email)->where('role', 'agent')->first();
                        if ($user) {
                            DB::table('reprogrammations')
                                ->where('id', $reprog->id)
                                ->update(['agent_id' => $user->id]);
                        }
                    }
                }
                
                // Recréer la clé étrangère vers users
                Schema::table('reprogrammations', function (Blueprint $table) {
                    $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
                });
            }
        }
        
        // Mettre à jour dossiers.agent_id si elle existe
        if (Schema::hasTable('dossiers')) {
            $hasAgentId = Schema::hasColumn('dossiers', 'agent_id');
            if ($hasAgentId) {
                // Supprimer l'ancienne clé étrangère
                try {
                    Schema::table('dossiers', function (Blueprint $table) {
                        $table->dropForeign(['agent_id']);
                    });
                } catch (\Exception $e) {
                    // Ignorer si la clé n'existe pas
                }
                
                // Mettre à jour les données
                $dossiers = DB::table('dossiers')->whereNotNull('agent_id')->get();
                foreach ($dossiers as $dossier) {
                    $agent = DB::table('agents')->where('id', $dossier->agent_id)->first();
                    if ($agent) {
                        $user = DB::table('users')->where('email', $agent->email)->where('role', 'agent')->first();
                        if ($user) {
                            DB::table('dossiers')
                                ->where('id', $dossier->id)
                                ->update(['agent_id' => $user->id]);
                        }
                    }
                }
                
                // Recréer la clé étrangère vers users
                Schema::table('dossiers', function (Blueprint $table) {
                    $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être facilement inversée
    }
};
