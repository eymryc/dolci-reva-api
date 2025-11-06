<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\HasMediaTrait;

class Lounge extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasMediaTrait;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'address',
        'city',
        'country',
        'opening_hours',
        'latitude',
        'longitude',
        'is_active',
        'age_restriction',
        'smoking_area',
        'outdoor_seating'
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_active' => 'boolean',
        'smoking_area' => 'boolean',
        'outdoor_seating' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Get the owner of the lounge.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the tables for the lounge.
     */
    public function tables(): HasMany
    {
        return $this->hasMany(LoungeTable::class);
    }

    /**
     * Get the bookings for the lounge.
     */
    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    /**
     * Get the amenities for the lounge.
     */
    public function amenities(): MorphToMany
    {
        return $this->morphToMany(Amenity::class, 'amenityable');
    }

    /**
     * Register media collections for the lounge.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Register media conversions for the lounge.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10);

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600)
            ->sharpen(10);

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(800)
            ->sharpen(10);
    }

    /**
     * Get available tables for a specific date and time.
     */
    public function getAvailableTables(string $date, string $time, int $guests): \Illuminate\Database\Eloquent\Collection
    {
        return $this->tables()
            ->where('is_active', true)
            ->where('capacity', '>=', $guests)
            ->whereDoesntHave('bookings', function ($query) use ($date, $time) {
                $query->where('start_date', $date)
                    ->where('status', '!=', 'CANCELLED');
            })
            ->get();
    }

    /**
     * Check if lounge is open at given time.
     */
    public function isOpenAt(string $date, string $time): bool
    {
        $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->format('l'));
        $openingHours = $this->opening_hours;

        if (!isset($openingHours[$dayOfWeek])) {
            return false;
        }

        $openTime = $openingHours[$dayOfWeek]['open'] ?? null;
        $closeTime = $openingHours[$dayOfWeek]['close'] ?? null;

        if (!$openTime || !$closeTime) {
            return false;
        }

        $currentTime = \Carbon\Carbon::parse($time);
        $open = \Carbon\Carbon::parse($openTime);
        $close = \Carbon\Carbon::parse($closeTime);

        return $currentTime->between($open, $close);
    }

    /**
     * Get recommended tables based on preferences.
     */
    public function getRecommendedTables(string $date, string $time, int $guests, string $preference = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->tables()
            ->where('is_active', true)
            ->where('capacity', '>=', $guests);

        // Filtres basés sur les préférences
        switch ($preference) {
            case 'intimate':
                $query->whereIn('table_type', ['sofa', 'private_booth']);
                break;
            case 'social':
                $query->whereIn('table_type', ['high_table', 'bar_counter']);
                break;
            case 'outdoor':
                $query->where('location', 'outdoor');
                break;
            case 'smoking':
                $query->where('location', 'smoking_area');
                break;
        }

        return $query->get();
    }
}