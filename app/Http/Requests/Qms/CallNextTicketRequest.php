<?php

namespace App\Http\Requests\Qms;

use Illuminate\Foundation\Http\FormRequest;

class CallNextTicketRequest extends FormRequest
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
            'centre_id' => 'required|exists:centres,id',
            'guichet_id' => 'required|exists:guichets,id',
            'ticket_id' => 'nullable|exists:tickets,id',
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
            'guichet_id.required' => 'Le guichet est obligatoire.',
            'guichet_id.exists' => 'Le guichet sélectionné n\'existe pas.',
            'ticket_id.exists' => 'Le ticket sélectionné n\'existe pas.',
        ];
    }
}

