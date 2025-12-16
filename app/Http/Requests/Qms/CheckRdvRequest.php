<?php

namespace App\Http\Requests\Qms;

use Illuminate\Foundation\Http\FormRequest;

class CheckRdvRequest extends FormRequest
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
            'numero' => 'required|string',
            'centre_id' => 'required|exists:centres,id',
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
            'numero.required' => 'Le numéro de rendez-vous est obligatoire.',
            'centre_id.required' => 'Le centre est obligatoire.',
            'centre_id.exists' => 'Le centre sélectionné n\'existe pas.',
        ];
    }
}

