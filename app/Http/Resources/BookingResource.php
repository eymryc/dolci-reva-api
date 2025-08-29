<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'customer_id' => $this->customer_id,
            'owner_id' => $this->owner_id,
            'bookable_type' => $this->bookable_type,
            'bookable_id' => $this->bookable_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_price' => $this->total_price,
            'commission_amount' => $this->commission_amount,
            'owner_amount' => $this->owner_amount,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
