<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'owner_id' => $this->owner_id,
            'bookable_type' => $this->bookable_type,
            'bookable_id' => $this->bookable_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'guests' => $this->guests,
            'booking_reference' => $this->booking_reference,
            'total_price' => $this->total_price,
            'commission_amount' => $this->commission_amount,
            'owner_amount' => $this->owner_amount,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at,
            'confirmed_at' => $this->confirmed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relations
            'customer' => $this->whenLoaded('customer', function () {
                return $this->customer;
            }),
            'owner' => $this->whenLoaded('owner', function () {
                return $this->owner;
            }),
            'bookable' => $this->whenLoaded('bookable', function () {
                return $this->bookable;
            }),
            
            // Relations spécifiques aux restaurants, lounges et night clubs
            'restaurant_tables' => $this->whenLoaded('restaurantTables', function () {
                return $this->restaurantTables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'table_number' => $table->table_number,
                        'capacity' => $table->capacity,
                        'location' => $table->location,
                        'table_type' => $table->table_type
                    ];
                });
            }),
            'lounge_tables' => $this->whenLoaded('loungeTables', function () {
                return $this->loungeTables->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'table_number' => $table->table_number,
                        'capacity' => $table->capacity,
                        'location' => $table->location,
                        'table_type' => $table->table_type
                    ];
                });
            }),
            'night_club_areas' => $this->whenLoaded('nightClubAreas', function () {
                return $this->nightClubAreas->map(function ($area) {
                    return [
                        'id' => $area->id,
                        'area_name' => $area->area_name,
                        'location' => $area->location,
                        'area_type' => $area->area_type
                    ];
                });
            }),
        ];
    }
}
