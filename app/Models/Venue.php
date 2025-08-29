<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'address_id',
        'category_id',
        'name',
        'description',
        'type',
        'capacity',
        'opening_hours',
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
        'opening_hours' => 'array',
    ];

    /**
     * Relation avec le propriétaire
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relation avec l'adresse
     */
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Relation avec la catégorie
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Accesseur pour le type lisible
     */
    public function getTypeLabelAttribute()
    {
        return [
            'RESTAURANT' => 'Restaurant',
            'BAR' => 'Bar',
            'LOUNGE' => 'Lounge',
            'SALLE_EVENT' => 'Salle d\'événement',
        ][$this->type] ?? $this->type;
    }

    /**
     * Formate les horaires d'ouverture
     */
    public function getFormattedOpeningHoursAttribute()
    {
        $days = [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche'
        ];

        $formatted = [];
        $hours = json_decode($this->opening_hours, true);

        foreach ($hours as $day => $time) {
            $formatted[$days[strtolower($day)]] = $time['open'] . ' - ' . $time['close'];
        }

        return $formatted;
    }
    
    /**
     * Relation avec les horaires d'ouverture
     */
    public function openingHours()
    {
        return $this->hasMany(VenueOpeningHour::class);
    }
}
