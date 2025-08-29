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
            'services'          => 'required|array',

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
