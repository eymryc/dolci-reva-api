<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NightClubBookingRequest extends FormRequest
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
            'guests' => 'required|integer|min:1|max:25',
            'notes' => 'nullable|string|max:500',
            'night_club_area_ids' => 'nullable|array',
            'night_club_area_ids.*' => 'integer|exists:night_club_areas,id'
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
            'guests.max' => 'Le nombre d\'invités ne peut pas dépasser 25.',
            'night_club_area_ids.array' => 'Les zones doivent être un tableau.',
            'night_club_area_ids.*.exists' => 'Une ou plusieurs zones sélectionnées n\'existent pas.'
        ];
    }
}