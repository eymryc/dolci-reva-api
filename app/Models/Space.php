<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Space extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'venue_id',
        'name',
        'type',
        'min_guests',
        'max_guests',
        'is_hourly_rate'
    ];

    protected $casts = [
        'type' => 'string',
        'is_hourly_rate' => 'boolean'
    ];

    /**
     * Relation avec le venue
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    /**
     * Accesseur pour le type lisible
     */
    public function getTypeLabelAttribute()
    {
        return [
            'TABLE' => 'Table',
            'SALON' => 'Salon privé',
            'PISTE' => 'Piste de danse'
        ][$this->type] ?? $this->type;
    }

    /**
     * Vérifie si l'espace peut accueillir un nombre donné de personnes
     */
    public function canAccommodate(int $guests): bool
    {
        return $guests >= $this->min_guests && $guests <= $this->max_guests;
    }

     public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }
}
