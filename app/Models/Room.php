<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{

    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'name',
        'description',
        // 'size_m2',
        'max_guests',
        // 'bedrooms',
        // 'bathrooms',
        'type',
        'standing',
        'price',
        'is_available',
        'is_active',
    ];

    /**
     * Relation avec les amenities
     */
    public function amenities()
    {
        return $this->morphToMany(Amenity::class, 'amenityable');
    }

    /**
     * Relation avec les images
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }


    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    /**
     * Relation avec la propriété
     */
    public function property()
    {
        return $this->belongsTo(Property::class);  
    }
}
