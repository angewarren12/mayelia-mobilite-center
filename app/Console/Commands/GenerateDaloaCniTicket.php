<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Models\Centre;
use App\Models\Service;
use App\Services\QmsPriorityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateDaloaCniTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qms:generate-daloa-cni';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer un ticket sans RDV pour Demande de CNI à Daloa';

    /**
     * Execute the console command.
     */
    public function handle(QmsPriorityService $priorityService)
    {
        $centreId = 2; // Daloa
        $serviceId = 4; // CNI

        try {
            DB::beginTransaction();

            $centre = Centre::findOrFail($centreId);
            $service = Service::findOrFail($serviceId);

            $prefix = strtoupper(substr($service->nom, 0, 1));
            
            // Compter les tickets du jour pour ce service/centre spécifique
            $count = Ticket::where('centre_id', $centreId)
                ->whereDate('created_at', Carbon::today())
                ->where('numero', 'like', $prefix . '%')
                ->lockForUpdate()
                ->count();
                
            $numero = $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            $ticket = Ticket::create([
                'numero' => $numero,
                'centre_id' => $centreId,
                'service_id' => $serviceId,
                'type' => 'sans_rdv',
                'priorite' => 1,
                'statut' => 'en_attente',
            ]);

            // Calculer la priorité
            $priorite = $priorityService->calculatePriority($centre, $ticket);
            $ticket->update(['priorite' => $priorite]);

            // Déclencher l'événement pour la TV
            event(new \App\Events\TicketCreated($ticket->fresh()));

            DB::commit();

            $this->info("Ticket généré avec succès !");
            $this->info("Numéro: {$numero}");
            $this->info("Centre: {$centre->nom}");
            $this->info("Service: {$service->nom}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Erreur: " . $e->getMessage());
        }
    }
}
