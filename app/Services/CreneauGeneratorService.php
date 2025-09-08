<?php

namespace App\Services;

use App\Models\Centre;
use App\Models\CreneauGenere;
use App\Models\TemplateCreneau;
use App\Models\Exception;
use Carbon\Carbon;

class CreneauGeneratorService
{
    /**
     * Génère les créneaux pour un centre sur une période donnée
     */
    public function generateCreneauxForCentre(Centre $centre, Carbon $startDate, Carbon $endDate)
    {
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $this->generateCreneauxForDate($centre, $currentDate);
            $currentDate->addDay();
        }
    }

    /**
     * Génère les créneaux pour une date spécifique
     */
    public function generateCreneauxForDate(Centre $centre, Carbon $date)
    {
        // Vérifier s'il y a une exception pour cette date
        $exception = Exception::where('centre_id', $centre->id)
            ->where('date_exception', $date->toDateString())
            ->first();

        if ($exception) {
            $this->handleException($centre, $date, $exception);
            return;
        }

        // Récupérer le jour de la semaine (1 = lundi, 7 = dimanche)
        $jourSemaine = $date->dayOfWeekIso;

        // Récupérer les templates pour ce jour de la semaine
        $templates = TemplateCreneau::where('centre_id', $centre->id)
            ->where('jour_semaine', $jourSemaine)
            ->where('statut', 'actif')
            ->with(['service', 'formule'])
            ->get();

        foreach ($templates as $template) {
            $this->createCreneauFromTemplate($template, $date);
        }
    }

    /**
     * Crée un créneau à partir d'un template
     */
    private function createCreneauFromTemplate(TemplateCreneau $template, Carbon $date)
    {
        // Parser la tranche horaire (ex: "07:00-08:00")
        $heures = explode('-', $template->tranche_horaire);
        $heureDebut = $heures[0];
        $heureFin = $heures[1];

        // Vérifier si le créneau existe déjà
        $existingCreneau = CreneauGenere::where('centre_id', $template->centre_id)
            ->where('service_id', $template->service_id)
            ->where('formule_id', $template->formule_id)
            ->where('date_creneau', $date->toDateString())
            ->where('heure_debut', $heureDebut)
            ->where('heure_fin', $heureFin)
            ->first();

        if ($existingCreneau) {
            return $existingCreneau;
        }

        return CreneauGenere::create([
            'centre_id' => $template->centre_id,
            'service_id' => $template->service_id,
            'formule_id' => $template->formule_id,
            'date_creneau' => $date->toDateString(),
            'heure_debut' => $heureDebut,
            'heure_fin' => $heureFin,
            'capacite_disponible' => $template->capacite,
            'capacite_totale' => $template->capacite,
            'statut' => 'disponible'
        ]);
    }

    /**
     * Gère les exceptions (centre fermé, horaires modifiés, etc.)
     */
    private function handleException(Centre $centre, Carbon $date, Exception $exception)
    {
        switch ($exception->type) {
            case 'ferme':
                // Supprimer tous les créneaux pour cette date
                CreneauGenere::where('centre_id', $centre->id)
                    ->where('date_creneau', $date->toDateString())
                    ->delete();
                break;

            case 'capacite_reduite':
                // Réduire la capacité de tous les créneaux
                CreneauGenere::where('centre_id', $centre->id)
                    ->where('date_creneau', $date->toDateString())
                    ->update([
                        'capacite_disponible' => $exception->capacite_reduite,
                        'capacite_totale' => $exception->capacite_reduite
                    ]);
                break;

            case 'horaires_modifies':
                // Supprimer les anciens créneaux et créer les nouveaux
                CreneauGenere::where('centre_id', $centre->id)
                    ->where('date_creneau', $date->toDateString())
                    ->delete();

                // Créer les nouveaux créneaux avec les horaires modifiés
                $this->createCreneauxWithModifiedHours($centre, $date, $exception);
                break;
        }
    }

    /**
     * Crée des créneaux avec des horaires modifiés
     */
    private function createCreneauxWithModifiedHours(Centre $centre, Carbon $date, Exception $exception)
    {
        // Récupérer les templates pour ce jour
        $jourSemaine = $date->dayOfWeekIso;
        $templates = TemplateCreneau::where('centre_id', $centre->id)
            ->where('jour_semaine', $jourSemaine)
            ->where('statut', 'actif')
            ->with(['service', 'formule'])
            ->get();

        foreach ($templates as $template) {
            // Adapter la tranche horaire aux nouveaux horaires
            $newTrancheHoraire = $this->adaptTrancheHoraire(
                $template->tranche_horaire,
                $exception->heure_debut,
                $exception->heure_fin,
                $exception->pause_debut,
                $exception->pause_fin
            );

            if ($newTrancheHoraire) {
                $heures = explode('-', $newTrancheHoraire);
                
                CreneauGenere::create([
                    'centre_id' => $template->centre_id,
                    'service_id' => $template->service_id,
                    'formule_id' => $template->formule_id,
                    'date_creneau' => $date->toDateString(),
                    'heure_debut' => $heures[0],
                    'heure_fin' => $heures[1],
                    'capacite_disponible' => $template->capacite,
                    'capacite_totale' => $template->capacite,
                    'statut' => 'disponible'
                ]);
            }
        }
    }

    /**
     * Adapte une tranche horaire aux nouveaux horaires
     */
    private function adaptTrancheHoraire($trancheHoraire, $nouvelleHeureDebut, $nouvelleHeureFin, $nouvellePauseDebut, $nouvellePauseFin)
    {
        $heures = explode('-', $trancheHoraire);
        $heureDebut = $heures[0];
        $heureFin = $heures[1];

        // Vérifier si la tranche est dans les nouveaux horaires
        if ($heureDebut >= $nouvelleHeureDebut && $heureFin <= $nouvelleHeureFin) {
            // Vérifier si la tranche ne chevauche pas avec la pause
            if ($nouvellePauseDebut && $nouvellePauseFin) {
                if ($heureDebut >= $nouvellePauseFin || $heureFin <= $nouvellePauseDebut) {
                    return $trancheHoraire;
                }
            } else {
                return $trancheHoraire;
            }
        }

        return null;
    }

    /**
     * Génère les créneaux pour les 6 prochains mois
     */
    public function generateCreneauxForNext6Months(Centre $centre)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addMonths(6);
        
        $this->generateCreneauxForCentre($centre, $startDate, $endDate);
    }
}
