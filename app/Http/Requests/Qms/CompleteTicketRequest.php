<?php

namespace App\Http\Requests\Qms;

use Illuminate\Foundation\Http\FormRequest;

class CompleteTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $ticket = $this->route('ticket');

        if (!$user || !$ticket) {
            return false;
        }

        // Vérifier que l'utilisateur a le droit de gérer les tickets
        if (!in_array($user->role, ['admin', 'agent'])) {
            return false;
        }

        // Vérifier l'accès au centre du ticket
        return $user->canAccessCentre($ticket->centre_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Pas de validation de données, l'autorisation suffit
        ];
    }
}
