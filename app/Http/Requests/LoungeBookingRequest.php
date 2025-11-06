<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoungeBookingRequest extends FormRequest
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
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'guests' => 'required|integer|min:1|max:15',
            'notes' => 'nullable|string|max:500',
            'lounge_table_ids' => 'nullable|array',
            'lounge_table_ids.*' => 'integer|exists:lounge_tables,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'La date de début est requise.',
            'start_date.after' => 'La date de début doit être dans le futur.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'guests.required' => 'Le nombre d\'invités est requis.',
            'guests.min' => 'Le nombre d\'invités doit être d\'au moins 1.',
            'guests.max' => 'Le nombre d\'invités ne peut pas dépasser 15.',
            'lounge_table_ids.array' => 'Les tables doivent être un tableau.',
            'lounge_table_ids.*.exists' => 'Une ou plusieurs tables sélectionnées n\'existent pas.'
        ];
    }
}