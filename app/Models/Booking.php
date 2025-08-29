<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{       
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use HasFactory, SoftDeletes;

    //
    protected $fillable = [
        'customer_id',
        'owner_id',
        'start_date',
        'end_date',
        'total_price',
        'status', 
        'payment_status', 
        'notes',
        'commission_amount',
        'owner_amount',
        'cancellation_reason',
    ];


    public function bookable()
    {
        return $this->morphTo();
    }
}
