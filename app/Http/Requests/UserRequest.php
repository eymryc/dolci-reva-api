<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'phone'             => 'required|string|max:255|unique:users,phone',
            'email'             => 'required|string|max:255|unique:users,email',
            'type'              => "required|in:CUSTOMER,OWNER,ADMIN,SUPER_ADMIN",
            'email_verified_at' => 'date',
            'password'          => 'required|string|max:255|confirmed',
            'remember_token'    => 'string|max:100',
            'services'          => 'nullable|array',
            'services.*'        => 'integer|exists:business_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.max' => 'Le prénom ne peut pas dépasser 255 caractères.',
            'last_name.required' => 'Le nom est obligatoire.',
            'last_name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'type.required' => 'Le type d\'utilisateur est obligatoire.',
            'type.in' => 'Le type d\'utilisateur doit être : CUSTOMER, OWNER, ADMIN ou SUPER_ADMIN.',
            'email_verified_at.date' => 'La date de vérification email doit être une date valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.max' => 'Le mot de passe ne peut pas dépasser 255 caractères.',
            'remember_token.max' => 'Le token de rappel ne peut pas dépasser 100 caractères.',
            'services.array' => 'Les services doivent être fournis sous forme de tableau.',
            'services.*.integer' => 'Chaque service doit être un identifiant valide.',
            'services.*.exists' => 'Un ou plusieurs services sélectionnés n\'existent pas.',
        ];
    }


    /**
     * Handle a failed validation attempt.
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
