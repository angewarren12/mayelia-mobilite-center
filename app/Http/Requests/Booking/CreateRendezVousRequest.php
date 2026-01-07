<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateRendezVousRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'centre_id' => 'required|exists:centres,id',
            'service_id' => 'required|exists:services,id',
            'formule_id' => 'required|exists:formules,id',
            'client_id' => 'nullable|exists:clients,id',
            'date_rendez_vous' => 'required|date|after_or_equal:today',
            'tranche_horaire' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            
            // Infos client
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'required|string|max:20',
            'prenom' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date|before:today',
            'lieu_naissance' => 'nullable|string|max:255',
            'sexe' => 'nullable|string|in:M,F',
            'adresse' => 'nullable|string|max:500',
            
            // Infos ONECI
            'oneci_data' => 'nullable|array',
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
            'centre_id.required' => 'Le centre est obligatoire.',
            'centre_id.exists' => 'Le centre sélectionné n\'existe pas.',
            'service_id.required' => 'Le service est obligatoire.',
            'service_id.exists' => 'Le service sélectionné n\'existe pas.',
            'formule_id.required' => 'La formule est obligatoire.',
            'formule_id.exists' => 'La formule sélectionnée n\'existe pas.',
            'date_rendez_vous.required' => 'La date du rendez-vous est obligatoire.',
            'date_rendez_vous.date' => 'La date du rendez-vous est invalide.',
            'date_rendez_vous.after_or_equal' => 'La date du rendez-vous ne peut pas être dans le passé.',
            'tranche_horaire.required' => 'La tranche horaire est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'sexe.in' => 'Le sexe doit être M ou F.',
        ];
    }
}

