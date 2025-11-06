<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\Bookable;
use App\Traits\HasMediaTrait;

class Residence extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, Bookable, InteractsWithMedia, HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'type',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'piece_number',
        'price',
        'standing',
        'average_rating',
        'total_ratings',
        'rating_count',
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
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'price' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    /**
     * Get the owner that owns the residence.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the amenities for the residence.
     */
    public function amenities(): MorphToMany
    {
        return $this->morphToMany(Amenity::class, 'amenityable');
    }

    /**
     * Get the opinions for the residence.
     */
    public function opinions()
    {
        return $this->hasMany(Opinion::class);
    }



    /**
     * Update the residence rating when a new rating is added.
     */
    public function updateRating(float $newRating): void
    {
        $this->total_ratings += $newRating;
        $this->rating_count += 1;
        $this->average_rating = $this->total_ratings / $this->rating_count;
        $this->save();
    }

    /**
     * Get the rating percentage for display.
     */
    public function getRatingPercentageAttribute(): float
    {
        return ($this->average_rating / 5.0) * 100;
    }

    /**
     * Check if the residence has any ratings.
     */
    public function hasRatings(): bool
    {
        return $this->rating_count > 0;
    }

    /**
     * Get the rating stars for display (1-5 stars).
     */
    public function getStarsAttribute(): int
    {
        return (int) round($this->average_rating);
    }

    /**
     * Get the bookings for this residence.
     */
    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    /**
     * Check if the residence is available for a specific date range.
     */
    public function isAvailableForDates($startDate, $endDate): bool
    {
        $conflictingBookings = $this->bookings()
            ->where('status', '!=', 'ANNULE')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        return !$conflictingBookings && $this->is_available;
    }

    /**
     * Get the next available date for this residence.
     */
    public function getNextAvailableDate(): ?string
    {
        // Si la résidence n'est pas active, elle n'est jamais disponible
        if (!$this->is_active) {
            return null;
        }

        // Si la résidence est disponible (is_available = true), elle est disponible maintenant
        if ($this->is_available) {
            return now()->toDateString();
        }

        // Si la résidence est occupée (is_available = false), on cherche la prochaine date de libération
        return $this->getNextAvailableDateFromBookings();
    }

    /**
     * Get all unavailable dates for this residence.
     */
    public function getUnavailableDates(): array
    {
        return $this->bookings()
            ->where('status', '!=', 'ANNULE')
            ->where('end_date', '>=', now()->toDateString())
            ->get()
            ->map(function ($booking) {
                return [
                    'start' => \Carbon\Carbon::parse($booking->start_date)->toDateString(),
                    'end' => \Carbon\Carbon::parse($booking->end_date)->toDateString(),
                    'status' => $booking->status
                ];
            })
            ->toArray();
    }

    /**
     * Get the availability status with human-readable message.
     */
    public function getAvailabilityStatus(): array
    {
        // Si la résidence n'est pas active, elle n'est jamais disponible
        if (!$this->is_active) {
            return [
                'status' => 'inactive',
                'message' => 'Cette résidence n\'est pas active',
                'next_available_date' => null
            ];
        }

        // is_available indique directement si la résidence est disponible ou occupée
        if ($this->is_available) {
            return [
                'status' => 'available',
                'message' => 'Disponible maintenant',
                'next_available_date' => now()->toDateString()
            ];
        } else {
            // Si occupée, on cherche la prochaine date de disponibilité
            $nextAvailableDate = $this->getNextAvailableDateFromBookings();
            if ($nextAvailableDate) {
                return [
                    'status' => 'occupied',
                    'message' => "Occupée jusqu'au " . \Carbon\Carbon::parse($nextAvailableDate)->format('d/m/Y'),
                    'next_available_date' => $nextAvailableDate
                ];
            } else {
                return [
                    'status' => 'occupied',
                    'message' => 'Occupée (date de libération inconnue)',
                    'next_available_date' => null
                ];
            }
        }
    }

    /**
     * Get the next available date based on bookings only (ignoring is_available flag).
     */
    public function getNextAvailableDateFromBookings(): ?string
    {
        $lastBooking = $this->bookings()
            ->where('status', '!=', 'ANNULE')
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('end_date', 'desc')
            ->first();

        if ($lastBooking) {
            return \Carbon\Carbon::parse($lastBooking->end_date)->addDay()->toDateString();
        }

        return now()->toDateString();
    }

    /**
     * Register media collections for the residence.
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
     * Register media conversions for the residence.
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
