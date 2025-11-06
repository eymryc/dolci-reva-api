<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NightClubRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'opening_hours' => 'required|array',
            'opening_hours.monday' => 'nullable|array',
            'opening_hours.monday.open' => 'nullable|date_format:H:i',
            'opening_hours.monday.close' => 'nullable|date_format:H:i',
            'opening_hours.tuesday' => 'nullable|array',
            'opening_hours.tuesday.open' => 'nullable|date_format:H:i',
            'opening_hours.tuesday.close' => 'nullable|date_format:H:i',
            'opening_hours.wednesday' => 'nullable|array',
            'opening_hours.wednesday.open' => 'nullable|date_format:H:i',
            'opening_hours.wednesday.close' => 'nullable|date_format:H:i',
            'opening_hours.thursday' => 'nullable|array',
            'opening_hours.thursday.open' => 'nullable|date_format:H:i',
            'opening_hours.thursday.close' => 'nullable|date_format:H:i',
            'opening_hours.friday' => 'nullable|array',
            'opening_hours.friday.open' => 'nullable|date_format:H:i',
            'opening_hours.friday.close' => 'nullable|date_format:H:i',
            'opening_hours.saturday' => 'nullable|array',
            'opening_hours.saturday.open' => 'nullable|date_format:H:i',
            'opening_hours.saturday.close' => 'nullable|date_format:H:i',
            'opening_hours.sunday' => 'nullable|array',
            'opening_hours.sunday.open' => 'nullable|date_format:H:i',
            'opening_hours.sunday.close' => 'nullable|date_format:H:i',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            
            // Champs spécifiques aux night clubs
            'age_restriction' => 'required|integer|in:18,21',
            'parking' => 'boolean',
            
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'integer|exists:amenities,id',
            'area_amenities' => 'nullable|array',
            'area_amenities.*.area_id' => 'required_with:area_amenities|integer|exists:night_club_areas,id',
            'area_amenities.*.amenities' => 'required_with:area_amenities|array',
            'area_amenities.*.amenities.*' => 'integer|exists:amenities,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du night club est obligatoire.',
            'description.required' => 'La description du night club est obligatoire.',
            'description.min' => 'La description doit contenir au moins 10 caractères.',
            'address.required' => 'L\'adresse du night club est obligatoire.',
            'city.required' => 'La ville est obligatoire.',
            'country.required' => 'Le pays est obligatoire.',
            'opening_hours.required' => 'Les heures d\'ouverture sont obligatoires.',
            'age_restriction.required' => 'La restriction d\'âge est obligatoire.',
            'age_restriction.integer' => 'La restriction d\'âge doit être un nombre entier.',
            'age_restriction.in' => 'La restriction d\'âge doit être 18 ou 21.',
            'capacity.integer' => 'La capacité doit être un nombre entier.',
            'capacity.min' => 'La capacité doit être d\'au moins 1 personne.',
            'capacity.max' => 'La capacité ne peut pas dépasser 5000 personnes.',
            'entry_fee.numeric' => 'Les frais d\'entrée doivent être un nombre.',
            'entry_fee.min' => 'Les frais d\'entrée ne peuvent pas être négatifs.',
            'entry_fee.max' => 'Les frais d\'entrée ne peuvent pas dépasser 1000€.',
            'music_genres.array' => 'Les genres musicaux doivent être un tableau.',
            'music_genres.*.in' => 'Le genre musical sélectionné n\'est pas valide.',
            'special_events.array' => 'Les événements spéciaux doivent être un tableau.',
            'special_events.*.day.required_with' => 'Le jour de l\'événement est obligatoire.',
            'special_events.*.day.in' => 'Le jour de l\'événement doit être un jour de la semaine valide.',
            'special_events.*.name.required_with' => 'Le nom de l\'événement est obligatoire.',
            'special_events.*.name.max' => 'Le nom de l\'événement ne peut pas dépasser 255 caractères.',
            'special_events.*.description.max' => 'La description de l\'événement ne peut pas dépasser 500 caractères.',
            'latitude.numeric' => 'La latitude doit être un nombre.',
            'latitude.between' => 'La latitude doit être entre -90 et 90.',
            'longitude.numeric' => 'La longitude doit être un nombre.',
            'longitude.between' => 'La longitude doit être entre -180 et 180.',
            'images.array' => 'Les images doivent être un tableau.',
            'images.*.image' => 'Chaque fichier doit être une image.',
            'images.*.mimes' => 'Les images doivent être au format JPEG, PNG, JPG ou WEBP.',
            'images.*.max' => 'Chaque image ne peut pas dépasser 2MB.',
            'amenities.array' => 'Les équipements doivent être un tableau.',
            'amenities.*.integer' => 'Chaque équipement doit être un identifiant valide.',
            'amenities.*.exists' => 'L\'équipement sélectionné n\'existe pas.'
        ];
    }
}