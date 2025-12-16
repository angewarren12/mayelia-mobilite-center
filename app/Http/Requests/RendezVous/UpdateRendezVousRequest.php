<?php

namespace App\Http\Requests\RendezVous;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRendezVousRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'centre_id' => 'required|exists:centres,id',
            'service_id' => 'required|exists:services,id',
            'formule_id' => 'required|exists:formules,id',
            'date_rendez_vous' => 'required|date',
            'tranche_horaire' => 'required|string|max:20',
            'statut' => 'required|in:confirme,annule,termine',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Le client est obligatoire.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'centre_id.required' => 'Le centre est obligatoire.',
            'centre_id.exists' => 'Le centre sélectionné n\'existe pas.',
            'service_id.required' => 'Le service est obligatoire.',
            'service_id.exists' => 'Le service sélectionné n\'existe pas.',
            'formule_id.required' => 'La formule est obligatoire.',
            'formule_id.exists' => 'La formule sélectionnée n\'existe pas.',
            'date_rendez_vous.required' => 'La date du rendez-vous est obligatoire.',
            'date_rendez_vous.date' => 'La date du rendez-vous est invalide.',
            'tranche_horaire.required' => 'La tranche horaire est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être : confirme, annule ou termine.',
        ];
    }
}

