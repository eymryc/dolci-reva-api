<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'address_id',
        'category_id',
        'title',
        'description',
        'type',
        'rental_type',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'size_m2',
        'tagline',
        'legal_name',
        'brand',
        'price',
        'piece_number',
        'state',
        'address',
        'street',
        'city',
        'country',
        'latitude',
        'longitude',
    ];

    // protected $casts = [
    //     'type' => PropertyType::class,
    //     'rental_type' => PropertyRentalType::class,
    // ];

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
     * Relation avec les médias
     */
    // public function media()
    // {
    //     return $this->morphMany(MediaAttachment::class, 'attachable')
    //                ->orderBy('display_order');
    // }

    /**
     * Accesseur pour le type de propriété
     */
    public function getTypeLabelAttribute()
    {
        return [
            'MAISON' => 'Maison',
            'APPARTEMENT' => 'Appartement',
            'HOTEL' => 'Hôtel',
            'RESIDENCE' => 'Résidence'
        ][$this->type] ?? $this->type;
    }

    /**
     * Accesseur pour le type de location
     */
    public function getRentalTypeLabelAttribute()
    {
        return [
            'ENTIER' => 'Logement entier',
            'CHAMBRE' => 'Chambre privée',
            'COLOCATION' => 'Colocation'
        ][$this->rental_type] ?? $this->rental_type;
    }

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
     * Relation avec les chambres
     */
    public function rooms()
    {
        return $this->hasMany(Room::class); 
    }
}
