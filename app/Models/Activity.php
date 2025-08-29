<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organizer_id',
        'address_id',
        'title',
        'description',
        'type',
        'duration_minutes',
        'equipment_provided',
        'price_per_person',
        'state',
        'address',
        'street',
        'city',
        'country',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'type' => 'string',
        'equipment_provided' => 'boolean',
        'price_per_person' => 'decimal:2'
    ];

    /**
     * Relation avec l'organisateur
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Relation avec l'adresse
     */
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Accesseur pour le type lisible
     */
    public function getTypeLabelAttribute()
    {
        return [
            'RANDO' => 'Randonnée',
            'VISITE' => 'Visite guidée',
            'ATELIER' => 'Atelier'
        ][$this->type] ?? $this->type;
    }

    /**
     * Formate la durée en heures/minutes
     */
    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        return $hours > 0
            ? sprintf('%dh%02d', $hours, $minutes)
            : sprintf('%d min', $minutes);
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }
}
