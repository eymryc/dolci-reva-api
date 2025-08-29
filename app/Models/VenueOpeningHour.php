<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VenueOpeningHour extends Model
{   
    use HasFactory, SoftDeletes;

    
    protected $fillable = ['venue_id', 'day', 'open', 'close'];

    /**
     * Relation avec le modèle Venue
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
