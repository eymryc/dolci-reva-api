<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organizer_id' => 'integer',
            'address_id' => 'integer',
            'title' => 'string|max:255',
            'description' => 'string',
            'type' => 'in:RANDO,VISITE,ATELIER',
            'duration_minutes' => 'integer',
            'equipment_provided' => 'integer',
            'price_per_person' => 'numeric',
             'address'       => 'required',
            'state'         => 'required',
            'street'        => 'nullable',
            'postal_code'   => 'nullable',
            'city'          => 'required|string|max:255',
            'country'       => 'required|string|max:255',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
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
