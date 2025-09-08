<?php

namespace App\Services;

use App\Models\Centre;
use App\Models\Exception;
use App\Models\JourTravail;
use App\Models\TemplateCreneau;
use App\Models\RendezVous;
use App\Models\Service;
use App\Models\Formule;
use Carbon\Carbon;

class DisponibiliteService
{
    /**
     * Calculer la disponibilité pour un centre et une date donnée
     */
    public function calculerDisponibilite($centreId, $date)
    {
        \Log::info('DisponibiliteService: Calcul pour centre ' . $centreId . ' et date ' . $date);
        
        $centre = Centre::find($centreId);
        if (!$centre) {
            \Log::error('DisponibiliteService: Centre non trouvé pour ID ' . $centreId);
            return null;
        }

        $dateCarbon = Carbon::parse($date);
        $jourSemaine = $dateCarbon->dayOfWeek; // 1 = Lundi, 2 = Mardi, etc.
        
        \Log::info('DisponibiliteService: Jour de la semaine: ' . $jourSemaine);

        // 1. Vérifier les exceptions (priorité MAX)
        $exception = $this->getExceptionForDate($centreId, $date);
        if ($exception) {
            return $this->processerException($exception, $centre);
        }

        // 2. Vérifier les jours ouvrables
        $jourTravail = $this->getJourTravail($centreId, $jourSemaine);
        \Log::info('DisponibiliteService: Jour de travail trouvé: ' . ($jourTravail ? 'OUI' : 'NON'));
        if ($jourTravail) {
            \Log::info('DisponibiliteService: Jour actif: ' . ($jourTravail->actif ? 'OUI' : 'NON'));
        }
        if (!$jourTravail || !$jourTravail->actif) {
            \Log::info('DisponibiliteService: Centre fermé - Jour non ouvrable');
            return $this->creerReponseFermee($centre, $date, 'Centre fermé - Jour non ouvrable');
        }

        // 3. Récupérer les templates pour ce jour
        $templates = $this->getTemplatesForDay($centreId, $jourSemaine);
        \Log::info('DisponibiliteService: Templates trouvés: ' . $templates->count());
        if ($templates->isEmpty()) {
            \Log::info('DisponibiliteService: Aucun template configuré');
            return $this->creerReponseFermee($centre, $date, 'Aucun template configuré');
        }

        // 4. Calculer les créneaux disponibles
        $creneauxDisponibles = $this->calculerCreneauxDisponibles(
            $centreId, 
            $date, 
            $jourTravail, 
            $templates
        );

        return [
            'date' => $date,
            'centre' => [
                'id' => $centre->id,
                'nom' => $centre->nom,
                'ville' => $centre->ville->nom
            ],
            'statut' => 'ouvert',
            'message' => 'Centre ouvert',
            'services' => $creneauxDisponibles,
            'jour_travail' => [
                'actif' => $jourTravail->actif,
                'heure_debut' => $jourTravail->heure_debut,
                'heure_fin' => $jourTravail->heure_fin,
                'pause_debut' => $jourTravail->pause_debut,
                'pause_fin' => $jourTravail->pause_fin,
                'intervalle' => $jourTravail->intervalle
            ]
        ];
    }

    /**
     * Récupérer l'exception pour une date donnée
     */
    private function getExceptionForDate($centreId, $date)
    {
        return Exception::where('centre_id', $centreId)
            ->where('date_exception', $date)
            ->first();
    }

    /**
     * Traiter une exception
     */
    private function processerException($exception, $centre)
    {
        $response = [
            'date' => $exception->date_exception->format('Y-m-d'),
            'centre' => [
                'id' => $centre->id,
                'nom' => $centre->nom,
                'ville' => $centre->ville->nom
            ],
            'exception' => [
                'type' => $exception->type,
                'description' => $exception->description,
                'type_formate' => $exception->type_formate
            ]
        ];

        switch ($exception->type) {
            case 'ferme':
                $response['statut'] = 'ferme';
                $response['message'] = 'Centre fermé - ' . $exception->description;
                $response['services'] = [];
                break;

            case 'capacite_reduite':
                $response['statut'] = 'capacite_reduite';
                $response['message'] = 'Capacité réduite - ' . $exception->description;
                $response['capacite_max'] = $exception->capacite_reduite;
                // Calculer les créneaux avec capacité réduite
                $response['services'] = $this->calculerCreneauxAvecCapaciteReduite($centre->id, $exception);
                break;

            case 'horaires_modifies':
                $response['statut'] = 'horaires_modifies';
                $response['message'] = 'Horaires modifiés - ' . $exception->description;
                $response['horaires_exception'] = [
                    'debut' => $exception->heure_debut,
                    'fin' => $exception->heure_fin,
                    'pause_debut' => $exception->pause_debut,
                    'pause_fin' => $exception->pause_fin
                ];
                // Calculer les créneaux avec horaires modifiés
                $response['services'] = $this->calculerCreneauxAvecHorairesModifies($centre->id, $exception);
                break;
        }

        return $response;
    }

    /**
     * Récupérer le jour de travail pour un jour de la semaine
     */
    private function getJourTravail($centreId, $jourSemaine)
    {
        return JourTravail::where('centre_id', $centreId)
            ->where('jour_semaine', $jourSemaine)
            ->first();
    }

    /**
     * Récupérer les templates pour un jour de la semaine
     */
    private function getTemplatesForDay($centreId, $jourSemaine)
    {
        return TemplateCreneau::where('centre_id', $centreId)
            ->where('jour_semaine', $jourSemaine)
            ->with(['service', 'formule'])
            ->get()
            ->groupBy('service_id');
    }

    /**
     * Calculer les créneaux disponibles
     */
    private function calculerCreneauxDisponibles($centreId, $date, $jourTravail, $templates)
    {
        $services = [];
        
        foreach ($templates as $serviceId => $templatesService) {
            $service = $templatesService->first()->service;
            $formules = $templatesService->groupBy('formule_id');
            
            $services[$serviceId] = [
                'id' => $service->id,
                'nom' => $service->nom,
                'description' => $service->description,
                'formules' => []
            ];

            foreach ($formules as $formuleId => $templatesFormule) {
                $formule = $templatesFormule->first()->formule;
                $creneaux = $this->calculerCreneauxPourFormule(
                    $centreId, 
                    $date, 
                    $jourTravail, 
                    $templatesFormule
                );

                $services[$serviceId]['formules'][$formuleId] = [
                    'id' => $formule->id,
                    'nom' => $formule->nom,
                    'prix' => $formule->prix,
                    'couleur' => $formule->couleur,
                    'creneaux' => $creneaux
                ];
            }
        }

        return $services;
    }

    /**
     * Calculer les créneaux pour une formule spécifique
     */
    private function calculerCreneauxPourFormule($centreId, $date, $jourTravail, $templates)
    {
        $creneaux = [];
        
        foreach ($templates as $template) {
            $capaciteTotale = $template->capacite;
            $rdvExistants = $this->compterRendezVous($centreId, $date, $template->tranche_horaire, $template->formule_id);
            $disponible = max(0, $capaciteTotale - $rdvExistants);

            $creneaux[] = [
                'tranche_horaire' => $template->tranche_horaire,
                'capacite_totale' => $capaciteTotale,
                'rdv_existants' => $rdvExistants,
                'disponible' => $disponible,
                'statut' => $this->determinerStatutCreneau($disponible, $capaciteTotale),
                'couleur' => $this->determinerCouleurCreneau($disponible, $capaciteTotale)
            ];
        }

        return $creneaux;
    }

    /**
     * Compter les rendez-vous existants pour un créneau
     */
    private function compterRendezVous($centreId, $date, $trancheHoraire, $formuleId)
    {
        return RendezVous::where('centre_id', $centreId)
            ->where('date_rendez_vous', $date)
            ->where('tranche_horaire', $trancheHoraire)
            ->where('formule_id', $formuleId)
            ->where('statut', '!=', 'annule')
            ->count();
    }

    /**
     * Déterminer le statut d'un créneau
     */
    private function determinerStatutCreneau($disponible, $capaciteTotale)
    {
        if ($disponible == 0) {
            return 'complet';
        } elseif ($disponible <= $capaciteTotale * 0.2) {
            return 'limite';
        } else {
            return 'disponible';
        }
    }

    /**
     * Déterminer la couleur d'un créneau
     */
    private function determinerCouleurCreneau($disponible, $capaciteTotale)
    {
        if ($disponible == 0) {
            return 'red';
        } elseif ($disponible <= $capaciteTotale * 0.2) {
            return 'yellow';
        } else {
            return 'green';
        }
    }

    /**
     * Créer une réponse pour un centre fermé
     */
    private function creerReponseFermee($centre, $date, $message)
    {
        return [
            'date' => $date,
            'centre' => [
                'id' => $centre->id,
                'nom' => $centre->nom,
                'ville' => $centre->ville->nom
            ],
            'statut' => 'ferme',
            'message' => $message,
            'services' => []
        ];
    }

    /**
     * Calculer les créneaux avec capacité réduite
     */
    private function calculerCreneauxAvecCapaciteReduite($centreId, $exception)
    {
        // Logique pour gérer la capacité réduite
        // À implémenter selon les besoins spécifiques
        return [];
    }

    /**
     * Calculer les créneaux avec horaires modifiés
     */
    private function calculerCreneauxAvecHorairesModifies($centreId, $exception)
    {
        // Logique pour gérer les horaires modifiés
        // À implémenter selon les besoins spécifiques
        return [];
    }
}
