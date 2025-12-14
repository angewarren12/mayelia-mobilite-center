<?php

namespace Database\Seeders;

use App\Models\Centre;
use App\Models\Guichet;
use App\Models\Service;
use Illuminate\Database\Seeder;

class GuichetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le centre de San Pedro
        $centre = Centre::where('nom', 'LIKE', '%San Pedro%')->first();
        
        if (!$centre) {
            $this->command->error('Centre San Pedro non trouvé !');
            return;
        }

        // Récupérer tous les services actifs
        $services = Service::where('statut', 'actif')->pluck('id')->toArray();

        // Créer 3 guichets
        $guichets = [
            [
                'nom' => 'Guichet 1',
                'centre_id' => $centre->id,
                'user_id' => null, // Pas d'agent assigné par défaut
                'statut' => 'fermé',
                'type_services' => $services, // Tous les services
            ],
            [
                'nom' => 'Guichet 2',
                'centre_id' => $centre->id,
                'user_id' => null,
                'statut' => 'fermé',
                'type_services' => $services,
            ],
            [
                'nom' => 'Guichet 3',
                'centre_id' => $centre->id,
                'user_id' => null,
                'statut' => 'fermé',
                'type_services' => $services,
            ],
        ];

        foreach ($guichets as $guichetData) {
            Guichet::create($guichetData);
        }

        $this->command->info('✅ 3 guichets créés pour le centre ' . $centre->nom);
    }
}
