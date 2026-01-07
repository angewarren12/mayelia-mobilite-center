<?php

namespace App\Http\Requests\Dossier;

use Illuminate\Foundation\Http\FormRequest;

class CreateWalkinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Client (nouveau ou existant)
            'client_id' => 'nullable|exists:clients,id',
            'client_nom' => 'required_without:client_id|string|max:255',
            'client_prenom' => 'required_without:client_id|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_telephone' => 'required_without:client_id|string|max:20',
            'client_date_naissance' => 'nullable|date|before:today',
            'client_lieu_naissance' => 'nullable|string|max:255',
            'client_adresse' => 'nullable|string|max:500',
            'client_profession' => 'nullable|string|max:255',
            'client_sexe' => 'nullable|string|in:M,F',
            'client_numero_piece_identite' => 'nullable|string|max:50',
            'client_type_piece_identite' => 'nullable|in:CNI,Passeport,Carte de résident,Autre',
            
            // Service et formule
            'service_id' => 'required|exists:services,id',
            'formule_id' => 'required|exists:formules,id',
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
            'client_nom.required_without' => 'Le nom du client est obligatoire si aucun client existant n\'est sélectionné.',
            'client_prenom.required_without' => 'Le prénom du client est obligatoire si aucun client existant n\'est sélectionné.',
            'client_email.required_without' => 'L\'email du client est obligatoire si aucun client existant n\'est sélectionné.',
            'client_email.email' => 'L\'email doit être une adresse email valide.',
            'client_telephone.required_without' => 'Le téléphone du client est obligatoire si aucun client existant n\'est sélectionné.',
            'service_id.required' => 'Le service est obligatoire.',
            'service_id.exists' => 'Le service sélectionné n\'existe pas.',
            'formule_id.required' => 'La formule est obligatoire.',
            'formule_id.exists' => 'La formule sélectionnée n\'existe pas.',
            'client_sexe.in' => 'Le sexe doit être M ou F.',
        ];
    }
}

