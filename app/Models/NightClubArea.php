<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class NightClubArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'night_club_id',
        'area_name',
        'location',
        'area_type',
        'is_active',
        'minimum_spend',
        'table_fee'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'minimum_spend' => 'decimal:2',
        'table_fee' => 'decimal:2'
    ];

    /**
     * Get the night club that owns the area.
     */
    public function nightClub(): BelongsTo
    {
        return $this->belongsTo(NightClub::class);
    }

    /**
     * Get the bookings for the area.
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'bookings_night_club_areas');
    }

    /**
     * Get the amenities for the area.
     */
    public function amenities(): MorphToMany
    {
        return $this->morphToMany(Amenity::class, 'amenityable');
    }

    /**
     * Check if area is available for a specific date and time.
     */
    public function isAvailableFor(string $date, string $time, int $guests): bool
    {
        // Check if area is active and has enough capacity
        if (!$this->is_active || $this->capacity < $guests) {
            return false;
        }

        // Check if there are conflicting bookings
        $conflictingBooking = $this->bookings()
            ->where('start_date', $date)
            ->where('status', '!=', 'CANCELLED')
            ->where(function ($query) use ($time) {
                $query->where('start_date', $date)
                    ->where('status', '!=', 'CANCELLED');
            })
            ->exists();

        return !$conflictingBooking;
    }

    /**
     * Get the display name for the area.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->area_name;
    }

    /**
     * Get the full location description.
     */
    public function getLocationDescriptionAttribute(): string
    {
        $locationMap = [
            'main_floor' => 'Étage principal',
            'vip' => 'Section VIP',
            'terrace' => 'Terrasse',
            'basement' => 'Sous-sol',
            'rooftop' => 'Toit-terrasse'
        ];

        return $locationMap[$this->location] ?? ucfirst($this->location);
    }

    /**
     * Get the area type description.
     */
    public function getTypeDescriptionAttribute(): string
    {
        $typeMap = [
            'dance_floor' => 'Piste de danse',
            'vip_booth' => 'Box VIP',
            'bar_area' => 'Zone bar',
            'terrace' => 'Terrasse',
            'private_room' => 'Salle privée',
            'bottle_service' => 'Service bouteilles'
        ];

        return $typeMap[$this->area_type] ?? ucfirst($this->area_type);
    }


    /**
     * Get minimum spend formatted as currency.
     */
    public function getMinimumSpendFormattedAttribute(): string
    {
        if (!$this->minimum_spend) {
            return 'Aucun minimum';
        }

        return number_format($this->minimum_spend, 2) . ' €';
    }

    /**
     * Get table fee formatted as currency.
     */
    public function getTableFeeFormattedAttribute(): string
    {
        if (!$this->table_fee) {
            return 'Gratuit';
        }

        return number_format($this->table_fee, 2) . ' €';
    }

    /**
     * Get amenities as string.
     */
    public function getAmenitiesStringAttribute(): string
    {
        if (!$this->amenities || $this->amenities->isEmpty()) {
            return 'Aucun';
        }

        return $this->amenities->pluck('name')->implode(', ');
    }

    /**
     * Get total cost (minimum spend + table fee).
     */
    public function getTotalCostAttribute(): float
    {
        return ($this->minimum_spend ?? 0) + ($this->table_fee ?? 0);
    }

    /**
     * Get total cost formatted as currency.
     */
    public function getTotalCostFormattedAttribute(): string
    {
        return number_format($this->total_cost, 2) . ' €';
    }
}