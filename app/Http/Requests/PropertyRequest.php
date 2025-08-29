<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => 'nullable|integer',
            'category_id' => 'integer',
            'name' => 'string|max:255',
            'description' => 'required',
            'address' => 'required|string',
            'state' => 'required|string',
            'street' => 'string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'max_guests' => 'integer',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'piece_number' => 'integer',
            'price' => 'required|numeric',
            'type' => 'required|in:STUDIO,APPARTEMENT,VILLA,DUPLEX,TRIPLEX',
            'rental_type' => 'required|in:ENTIER,COLOCATION',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'amenities' => 'required|array',
            'amenities.*' => 'integer|exists:amenities,id',
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
