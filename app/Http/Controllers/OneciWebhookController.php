<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OneciVerificationService;
use App\Models\RendezVous;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur pour recevoir les webhooks ONECI
 * 
 * Ce contrôleur gère les notifications de changement de statut
 * envoyées par l'API ONECI
 */
class OneciWebhookController extends Controller
{
    protected OneciVerificationService $oneciService;

    public function __construct(OneciVerificationService $oneciService)
    {
        $this->oneciService = $oneciService;
    }

    /**
     * Reçoit les mises à jour de statut depuis ONECI
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function receiveStatusUpdate(Request $request)
    {
        Log::info('Webhook ONECI reçu', [
            'payload' => $request->all()
        ]);

        // Validation des données reçues
        $validator = Validator::make($request->all(), [
            'numero_pre_enrolement' => 'required|string',
            'statut' => 'required|in:en_attente,valide,rejete',
            'nom' => 'required|string',
            'prenoms' => 'required|string',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            Log::error('Webhook ONECI - Validation échouée', [
                'errors' => $validator->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Vérifier si un rendez-vous existe déjà avec ce numéro
            $rendezVous = RendezVous::where('numero_pre_enrolement', $request->numero_pre_enrolement)
                ->first();

            if ($rendezVous) {
                // Mettre à jour le rendez-vous existant
                $rendezVous->update([
                    'statut_oneci' => $request->statut,
                    'donnees_oneci' => $request->all(),
                    'verified_at' => now()
                ]);

                Log::info('Rendez-vous mis à jour via webhook ONECI', [
                    'rendez_vous_id' => $rendezVous->id,
                    'statut' => $request->statut
                ]);
            } else {
                // Créer un nouveau rendez-vous en attente
                // (le client pourra compléter la réservation plus tard)
                $token = $this->oneciService->generateVerificationToken();
                
                $rendezVous = RendezVous::create([
                    'numero_pre_enrolement' => $request->numero_pre_enrolement,
                    'token_verification' => $token,
                    'statut_oneci' => $request->statut,
                    'donnees_oneci' => $request->all(),
                    'verified_at' => now(),
                    'statut' => RendezVous::STATUT_CONFIRME // Statut par défaut
                ]);

                Log::info('Nouveau rendez-vous créé via webhook ONECI', [
                    'rendez_vous_id' => $rendezVous->id,
                    'token' => $token
                ]);
            }

            // Si le statut est validé, on pourrait envoyer un email/SMS au client
            // avec le lien de prise de rendez-vous
            if ($request->statut === 'valide' && $rendezVous->token_verification) {
                $this->sendBookingLink($rendezVous);
            }

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'data' => [
                    'numero_pre_enrolement' => $rendezVous->numero_pre_enrolement,
                    'statut' => $rendezVous->statut_oneci
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du webhook ONECI', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement de la requête'
            ], 500);
        }
    }

    /**
     * Envoie le lien de prise de rendez-vous au client
     * 
     * @param RendezVous $rendezVous
     * @return void
     */
    private function sendBookingLink(RendezVous $rendezVous): void
    {
        // TODO: Implémenter l'envoi d'email/SMS avec le lien
        // Le lien sera: route('booking.verify-token', ['token' => $rendezVous->token_verification])
        
        Log::info('Lien de prise de rendez-vous généré', [
            'numero_pre_enrolement' => $rendezVous->numero_pre_enrolement,
            'token' => $rendezVous->token_verification,
            'url' => route('booking.verify-token', ['token' => $rendezVous->token_verification])
        ]);

        // Exemple d'implémentation future:
        // Mail::to($rendezVous->donnees_oneci['email'])
        //     ->send(new BookingLinkMail($rendezVous));
    }
}
