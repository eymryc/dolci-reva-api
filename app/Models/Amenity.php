<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenity extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Relation avec les propriétés
     */
    public function properties()
    {
        return $this->morphedByMany(Property::class, 'amenityable');
    }

    /**
     * Relation avec les chambres
     */
    public function rooms()
    {
        return $this->morphedByMany(Room::class, 'amenityable');
    }
}
