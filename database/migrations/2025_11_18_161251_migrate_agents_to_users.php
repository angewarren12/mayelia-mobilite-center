<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrer les agents vers users
        if (Schema::hasTable('agents')) {
            $agents = DB::table('agents')->get();
            
            foreach ($agents as $agent) {
                // Vérifier si un user avec cet email existe déjà
                $existingUser = DB::table('users')->where('email', $agent->email)->first();
                
                if (!$existingUser) {
                    // Créer un nouveau user avec le rôle agent
                    DB::table('users')->insert([
                        'nom' => $agent->nom,
                        'prenom' => $agent->prenom,
                        'email' => $agent->email,
                        'telephone' => $agent->telephone,
                        'centre_id' => $agent->centre_id,
                        'role' => 'agent',
                        'statut' => $agent->actif ? 'actif' : 'inactif',
                        'derniere_connexion' => $agent->derniere_connexion,
                        'password' => Hash::make('password'), // Mot de passe par défaut, à changer
                        'created_at' => $agent->created_at,
                        'updated_at' => $agent->updated_at,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être inversée facilement
        // car on ne peut pas distinguer les users créés manuellement des agents migrés
    }
};
