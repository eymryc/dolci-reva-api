<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'integer',
            'owner_id' => 'integer',
            'bookable_type' => 'string|max:255',
            'bookable_id' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'total_price' => 'numeric',
            'commission_amount' => 'numeric',
            'owner_amount' => 'numeric',
            'status' => 'in:CONFIRME,ANNULE,EN_ATTENTE',
            'payment_status' => 'in:PAYE,REMBOURSE,EN_ATTENTE',
            'notes' => 'string',
            'cancellation_reason' => 'string',
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
