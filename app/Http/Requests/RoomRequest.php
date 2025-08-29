<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => 'integer',
            'name' => 'string|max:255',
            'description' => 'string',
            'max_guests' => 'integer',
            'price' => 'numeric',
            'type' => 'in:SIMPLE,DOUBLE,TWIN,TRIPLE,QUAD',
            'standing' => 'in:STANDARD,DELUXE,EXÉCUTIVE,SUITE,SUITE JUNIOR,SUITE EXÉCUTIVE,SUITE PRÉSIDENTIELLE',
            'is_available' => 'integer',
            'is_active' => 'integer',
            'deleted_at' => 'date',
        ];
    }


    /**
     * Handle a failed validation attempt.
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
