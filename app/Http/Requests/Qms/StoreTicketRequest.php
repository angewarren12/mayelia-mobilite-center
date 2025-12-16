<?php

namespace App\Http\Requests\Qms;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Route publique pour kiosk
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
            'service_id' => 'nullable|exists:services,id',
            'type' => 'required|in:rdv,sans_rdv',
            'numero_rdv' => 'nullable|string|required_if:type,rdv',
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
            'service_id.exists' => 'Le service sélectionné n\'existe pas.',
            'type.required' => 'Le type de ticket est obligatoire.',
            'type.in' => 'Le type doit être "rdv" ou "sans_rdv".',
            'numero_rdv.required_if' => 'Le numéro de rendez-vous est obligatoire pour un ticket avec RDV.',
        ];
    }
}

