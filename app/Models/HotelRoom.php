<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\Bookable;
use App\Traits\HasMediaTrait;

class HotelRoom extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, Bookable, InteractsWithMedia, HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hotel_id',
        'room_number',
        'name',
        'description',
        'type',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'price',
        'standing',
        'is_available',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the hotel that owns the room.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the amenities for the hotel room.
     */
    public function amenities()
    {
        return $this->morphToMany(Amenity::class, 'amenityable');
    }


    /**
     * Generate a default name for the room based on type, standing and room number.
     */
    public function generateDefaultName(): string
    {
        $parts = [];
        
        // Add standing if not STANDARD
        if ($this->standing && $this->standing !== 'STANDARD') {
            $parts[] = ucfirst(strtolower($this->standing));
        }
        
        // Add type
        if ($this->type) {
            $parts[] = ucfirst(strtolower($this->type));
        }
        
        // Add room number if available
        if ($this->room_number) {
            $parts[] = "Chambre {$this->room_number}";
        } else {
            $parts[] = "Chambre";
        }
        
        return implode(' ', $parts);
    }

    /**
     * Get the display name (generated if name is null).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? $this->generateDefaultName();
    }

    /**
     * Register media collections for the hotel room.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Register media conversions for the hotel room.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('images', 'gallery');

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('images', 'gallery');

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(800)
            ->sharpen(10)
            ->performOnCollections('images', 'gallery');
    }
}
