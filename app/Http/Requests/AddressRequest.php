<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address'       => 'required',
            'state'         => 'required',
            'street'        => 'nullable',
            'postal_code'   => 'nullable',
            'city'          => 'required|string|max:255',
            'country'       => 'required|string|max:255',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'place_id'      => 'nullable',
            'user_id'       => 'integer',
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
