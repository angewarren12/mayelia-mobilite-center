<?php

namespace App\Http\Requests\Dossier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDossierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $dossier = $this->route('dossier');
        $user = $this->user();

        if (!$dossier || !$user) {
            return false;
        }

        return $user->canAccessCentre($dossier->rendezVous->centre_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'statut' => 'required|in:en_cours,complet,rejete',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
