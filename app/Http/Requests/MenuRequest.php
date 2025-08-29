<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'venue_id' => 'integer',
            'name' => 'string|max:255',
            'description' => 'string',
            'type' => 'in:PETIT_DEJEUNER,DEJEUNER,DINER,BOISSONS,ENFANTS',
            'is_active' => 'integer',
            'start_time' => 'string',
            'end_time' => 'string',
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
