<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\JourTravail;
use App\Models\TemplateCreneau;

class TrancheHoraireService
{
    /**
     * Génère les tranches horaires pour un jour de travail donné
     */
    public function generateTranchesForDay(JourTravail $jourTravail): array
    {
        $tranches = [];
        $intervalle = $jourTravail->intervalle_minutes;
        
        $heureDebut = Carbon::parse($jourTravail->heure_debut);
        $heureFin = Carbon::parse($jourTravail->heure_fin);
        $pauseDebut = $jourTravail->pause_debut ? Carbon::parse($jourTravail->pause_debut) : null;
        $pauseFin = $jourTravail->pause_fin ? Carbon::parse($jourTravail->pause_fin) : null;
        
        $current = $heureDebut->copy();
        
        while ($current->lt($heureFin)) {
            $next = $current->copy()->addMinutes($intervalle);
            
            // Vérifier si on dépasse l'heure de fin
            if ($next->gt($heureFin)) {
                break;
            }
            
            // Vérifier si cette tranche est dans la pause
            $estPause = $this->isInPause($current, $next, $pauseDebut, $pauseFin);
            
            $tranches[] = [
                'debut' => $current->copy(),
                'fin' => $next->copy(),
                'tranche_horaire' => $current->format('H:i') . ':00 - ' . $next->format('H:i') . ':00',
                'est_pause' => $estPause,
                'duree_minutes' => $intervalle
            ];
            
            $current = $next;
        }
        
        return $tranches;
    }
    
    /**
     * Vérifie si une tranche horaire est dans la pause
     */
    private function isInPause(Carbon $debut, Carbon $fin, ?Carbon $pauseDebut, ?Carbon $pauseFin): bool
    {
        if (!$pauseDebut || !$pauseFin) {
            return false;
        }
        
        // Une tranche est en pause si elle chevauche avec la période de pause
        return $debut->lt($pauseFin) && $fin->gt($pauseDebut);
    }
    
    /**
     * Génère les tranches horaires pour tous les jours actifs d'un centre
     */
    public function generateTranchesForCentre(int $centreId): array
    {
        $joursTravail = JourTravail::where('centre_id', $centreId)
            ->where('actif', true)
            ->orderBy('jour_semaine')
            ->get();
            
        $tranchesParJour = [];
        
        foreach ($joursTravail as $jour) {
            $tranchesParJour[$jour->jour_semaine] = $this->generateTranchesForDay($jour);
        }
        
        return $tranchesParJour;
    }
    
    /**
     * Vérifie les conflits de templates pour une tranche horaire
     */
    public function checkConflicts(int $centreId, int $serviceId, int $jourSemaine, string $trancheHoraire): array
    {
        $conflicts = [];
        
        // Récupérer tous les templates existants pour cette tranche
        $templates = TemplateCreneau::where('centre_id', $centreId)
            ->where('jour_semaine', $jourSemaine)
            ->where('tranche_horaire', $trancheHoraire)
            ->with(['service', 'formule'])
            ->get();
            
        if ($templates->isEmpty()) {
            return $conflicts;
        }
        
        // Grouper par service
        $templatesParService = $templates->groupBy('service_id');
        
        foreach ($templatesParService as $serviceIdGroup => $templatesService) {
            $service = $templatesService->first()->service;
            $totalCapacite = $templatesService->sum('capacite');
            
            $conflicts[] = [
                'service' => $service,
                'templates' => $templatesService,
                'total_capacite' => $totalCapacite,
                'nb_formules' => $templatesService->count()
            ];
        }
        
        return $conflicts;
    }
    
    /**
     * Calcule la capacité totale disponible pour une tranche horaire
     */
    public function getTotalCapacity(int $centreId, int $jourSemaine, string $trancheHoraire): int
    {
        return TemplateCreneau::where('centre_id', $centreId)
            ->where('jour_semaine', $jourSemaine)
            ->where('tranche_horaire', $trancheHoraire)
            ->sum('capacite');
    }
    
    /**
     * Valide qu'un changement de configuration est compatible avec les templates existants
     */
    public function validateConfigurationChange(JourTravail $jourTravail, array $nouvelleConfig): array
    {
        $errors = [];
        $warnings = [];
        
        // Récupérer tous les templates existants pour ce jour
        $templates = TemplateCreneau::where('centre_id', $jourTravail->centre_id)
            ->where('jour_semaine', $jourTravail->jour_semaine)
            ->with(['service', 'formule'])
            ->get();
            
        if ($templates->isEmpty()) {
            return ['errors' => $errors, 'warnings' => $warnings]; // Pas de conflit si aucun template
        }
        
        // Créer un objet temporaire avec la nouvelle configuration
        $nouveauJourTravail = clone $jourTravail;
        $nouveauJourTravail->intervalle_minutes = $nouvelleConfig['intervalle_minutes'] ?? $jourTravail->intervalle_minutes;
        $nouveauJourTravail->heure_debut = $nouvelleConfig['heure_debut'] ?? $jourTravail->heure_debut;
        $nouveauJourTravail->heure_fin = $nouvelleConfig['heure_fin'] ?? $jourTravail->heure_fin;
        $nouveauJourTravail->pause_debut = $nouvelleConfig['pause_debut'] ?? $jourTravail->pause_debut;
        $nouveauJourTravail->pause_fin = $nouvelleConfig['pause_fin'] ?? $jourTravail->pause_fin;
        
        // Générer les nouvelles tranches
        $nouvellesTranches = $this->generateTranchesForDay($nouveauJourTravail);
        $nouvellesTranchesHoraires = collect($nouvellesTranches)->pluck('tranche_horaire')->toArray();
        
        // Analyser chaque template existant
        foreach ($templates as $template) {
            $templateInfo = "Template '{$template->formule->nom}' pour '{$template->service->nom}' ({$template->tranche_horaire})";
            
            if (!in_array($template->tranche_horaire, $nouvellesTranchesHoraires)) {
                // Le template n'existe plus dans les nouvelles tranches
                $errors[] = "{$templateInfo} ne sera plus valide avec la nouvelle configuration.";
            } else {
                // Vérifier si la tranche est maintenant en pause
                $trancheData = collect($nouvellesTranches)->firstWhere('tranche_horaire', $template->tranche_horaire);
                if ($trancheData && $trancheData['est_pause']) {
                    $warnings[] = "{$templateInfo} sera maintenant en période de pause.";
                }
            }
        }
        
        return ['errors' => $errors, 'warnings' => $warnings];
    }
    
    /**
     * Valide qu'un nouvel intervalle est compatible avec les templates existants
     */
    public function validateIntervalleChange(JourTravail $jourTravail, int $nouvelIntervalle): array
    {
        return $this->validateConfigurationChange($jourTravail, ['intervalle_minutes' => $nouvelIntervalle]);
    }
    
    /**
     * Migre les templates existants vers une nouvelle configuration
     */
    public function migrateTemplatesToNewConfiguration(JourTravail $jourTravail, array $nouvelleConfig): array
    {
        $migrated = [];
        $deleted = [];
        $warnings = [];
        
        // Récupérer tous les templates existants
        $templates = TemplateCreneau::where('centre_id', $jourTravail->centre_id)
            ->where('jour_semaine', $jourTravail->jour_semaine)
            ->with(['service', 'formule'])
            ->get();
            
        if ($templates->isEmpty()) {
            return ['migrated' => $migrated, 'deleted' => $deleted, 'warnings' => $warnings];
        }
        
        // Créer un objet temporaire avec la nouvelle configuration
        $nouveauJourTravail = clone $jourTravail;
        $nouveauJourTravail->intervalle_minutes = $nouvelleConfig['intervalle_minutes'] ?? $jourTravail->intervalle_minutes;
        $nouveauJourTravail->heure_debut = $nouvelleConfig['heure_debut'] ?? $jourTravail->heure_debut;
        $nouveauJourTravail->heure_fin = $nouvelleConfig['heure_fin'] ?? $jourTravail->heure_fin;
        $nouveauJourTravail->pause_debut = $nouvelleConfig['pause_debut'] ?? $jourTravail->pause_debut;
        $nouveauJourTravail->pause_fin = $nouvelleConfig['pause_fin'] ?? $jourTravail->pause_fin;
        
        // Générer les nouvelles tranches
        $nouvellesTranches = $this->generateTranchesForDay($nouveauJourTravail);
        $nouvellesTranchesHoraires = collect($nouvellesTranches)->pluck('tranche_horaire')->toArray();
        
        foreach ($templates as $template) {
            $templateInfo = "Template '{$template->formule->nom}' pour '{$template->service->nom}' ({$template->tranche_horaire})";
            
            if (in_array($template->tranche_horaire, $nouvellesTranchesHoraires)) {
                // Le template est compatible
                $trancheData = collect($nouvellesTranches)->firstWhere('tranche_horaire', $template->tranche_horaire);
                
                if ($trancheData && $trancheData['est_pause']) {
                    // La tranche est maintenant en pause, on supprime le template
                    $template->delete();
                    $deleted[] = "{$templateInfo} supprimé car maintenant en période de pause.";
                } else {
                    // Le template reste valide
                    $migrated[] = $template;
                }
            } else {
                // Le template n'est plus compatible, on le supprime
                $template->delete();
                $deleted[] = "{$templateInfo} supprimé car incompatible avec la nouvelle configuration.";
            }
        }
        
        return ['migrated' => $migrated, 'deleted' => $deleted, 'warnings' => $warnings];
    }
    
    /**
     * Migre les templates existants vers un nouvel intervalle
     */
    public function migrateTemplatesToNewIntervalle(JourTravail $jourTravail, int $nouvelIntervalle): array
    {
        return $this->migrateTemplatesToNewConfiguration($jourTravail, ['intervalle_minutes' => $nouvelIntervalle]);
    }
}
