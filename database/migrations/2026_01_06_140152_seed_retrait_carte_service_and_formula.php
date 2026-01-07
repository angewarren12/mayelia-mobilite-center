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
        // Nettoyage préalable si nécessaire
        $existingService = DB::table('services')->where('nom', 'Retrait de Carte')->first();
        if ($existingService) {
            DB::table('centre_services')->where('service_id', $existingService->id)->delete();
            DB::table('formules')->where('service_id', $existingService->id)->delete();
            DB::table('services')->where('id', $existingService->id)->delete();
        }

        // 1. Créer le service
        $serviceId = DB::table('services')->insertGetId([
            'nom' => 'Retrait de Carte',
            'description' => 'Service de remise physique des pièces (CNI / Résident)',
            'statut' => 'actif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Créer la formule
        DB::table('formules')->insert([
            'service_id' => $serviceId,
            'nom' => 'Standard',
            'prix' => 0,
            'couleur' => '#02913F', // Green Mayelia
            'statut' => 'actif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Associer le service à tous les centres existants
        $centres = DB::table('centres')->pluck('id');
        foreach ($centres as $centreId) {
            DB::table('centre_services')->insert([
                'centre_id' => $centreId,
                'service_id' => $serviceId,
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $service = DB::table('services')->where('nom', 'Retrait de Carte')->first();
        if ($service) {
            DB::table('centre_services')->where('service_id', $service->id)->delete();
            DB::table('formules')->where('service_id', $service->id)->delete();
            DB::table('services')->where('id', $service->id)->delete();
        }
    }
};
