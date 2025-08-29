<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_id' => 'integer',
            'name' => 'string|max:255',
            'description' => 'string',
            'category' => 'in:ENTREE,PLAT,DESSERT,BOISSON',
            'price' => 'numeric',
            'is_vegetarian' => 'integer',
            'is_vegan' => 'integer',
            'is_gluten_free' => 'integer',
            'position' => 'integer',
            'image_url' => 'string|max:255',
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
