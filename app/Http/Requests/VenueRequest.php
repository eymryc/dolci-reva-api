<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => 'integer',
            'address_id' => 'integer',
            'category_id' => 'integer',
            'name' => 'string|max:255',
            'description' => 'string',
            'type' => 'in:RESTAURANT,BAR,LOUNGE,SALLE_EVENT',
            'capacity' => 'integer',
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
