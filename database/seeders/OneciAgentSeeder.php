<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OneciAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'agent ONECI existe déjà
        $oneciAgent = User::where('email', 'oneci@mayelia.com')->first();

        if (!$oneciAgent) {
            $oneciAgent = User::create([
                'nom' => 'ONECI',
                'prenom' => 'Agent',
                'email' => 'oneci@mayelia.com',
                'password' => Hash::make('oneci123'),
                'telephone' => '+225 01 23 45 67 89',
                'role' => 'oneci',
                'statut' => 'actif'
            ]);

            $this->command->info('✅ Agent ONECI créé avec succès !');
            $this->command->info('📧 Email: oneci@mayelia.com');
            $this->command->info('🔑 Mot de passe: oneci123');
        } else {
            $this->command->info('ℹ️  Agent ONECI existe déjà.');
        }
    }
}
