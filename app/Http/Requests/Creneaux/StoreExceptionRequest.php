<?php

namespace App\Http\Requests\Creneaux;

use Illuminate\Foundation\Http\FormRequest;

class StoreExceptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('creneaux.exceptions.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_exception' => 'required|date|after_or_equal:today',
            'type' => 'required|in:ferme,capacite_reduite,horaires_modifies',
            'description' => 'nullable|string|max:255',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
            'pause_debut' => 'nullable|date_format:H:i',
            'pause_fin' => 'nullable|date_format:H:i|after:pause_debut',
            'capacite_reduite' => 'nullable|integer|min:1'
        ];
    }
}
