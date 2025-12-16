<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Services\QmsPriorityService;
use Illuminate\Support\Facades\Log;

class RecalculateTicketPriorities
{
    protected $priorityService;

    /**
     * Create the event listener.
     */
    public function __construct(QmsPriorityService $priorityService)
    {
        $this->priorityService = $priorityService;
    }

    /**
     * Handle the event.
     */
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;
        
        // Recalculer les priorités pour tous les tickets en attente du même centre
        $this->priorityService->updateAllPriorities($ticket->centre);
        
        Log::debug('Priorités recalculées après création de ticket', [
            'ticket_id' => $ticket->id,
            'centre_id' => $ticket->centre_id,
        ]);
    }
}

