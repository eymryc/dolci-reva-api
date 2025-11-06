<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Vérifier la disponibilité d'un élément bookable
     */
    public function checkAvailability($bookable, Carbon $startDate, Carbon $endDate)
    {
        $conflictingBookings = Booking::where('bookable_type', get_class($bookable))
            ->where('bookable_id', $bookable->id)
            ->where('status', 'CONFIRME')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();
            
        return !$conflictingBookings;
    }
    
    /**
     * Obtenir les créneaux disponibles pour une période
     */
    public function getAvailableSlots($bookable, Carbon $startDate, Carbon $endDate)
    {
        $bookings = Booking::where('bookable_type', get_class($bookable))
            ->where('bookable_id', $bookable->id)
            ->where('status', 'CONFIRME')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orderBy('start_date')
            ->get();
            
        $availableSlots = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();
            
            $dayBookings = $bookings->filter(function($booking) use ($dayStart, $dayEnd) {
                return $booking->start_date->between($dayStart, $dayEnd) ||
                       $booking->end_date->between($dayStart, $dayEnd) ||
                       ($booking->start_date->lte($dayStart) && $booking->end_date->gte($dayEnd));
            });
            
            if ($dayBookings->isEmpty()) {
                $availableSlots[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'available' => true,
                    'bookings' => []
                ];
            } else {
                $availableSlots[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'available' => false,
                    'bookings' => $dayBookings->map(function($booking) {
                        return [
                            'start_time' => $booking->start_date->format('H:i'),
                            'end_time' => $booking->end_date->format('H:i'),
                            'status' => $booking->status
                        ];
                    })->toArray()
                ];
            }
            
            $currentDate->addDay();
        }
        
        return $availableSlots;
    }
    
    /**
     * Vérifier la capacité d'accueil
     */
    public function checkCapacity($bookable, int $guests)
    {
        if (method_exists($bookable, 'max_guests')) {
            return $guests <= $bookable->max_guests;
        }
        
        if (method_exists($bookable, 'max_guests')) {
            return $guests <= $bookable->max_guests;
        }
        
        return true; // Par défaut, on accepte
    }
    
    /**
     * Obtenir les statistiques de disponibilité
     */
    public function getAvailabilityStats($bookable, Carbon $startDate, Carbon $endDate)
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        
        $bookings = Booking::where('bookable_type', get_class($bookable))
            ->where('bookable_id', $bookable->id)
            ->where('status', 'CONFIRME')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get();
            
        $bookedDays = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $hasBooking = $bookings->filter(function($booking) use ($currentDate) {
                return $currentDate->between($booking->start_date, $booking->end_date);
            })->isNotEmpty();
            
            if ($hasBooking) {
                $bookedDays++;
            }
            
            $currentDate->addDay();
        }
        
        $availableDays = $totalDays - $bookedDays;
        $occupancyRate = $totalDays > 0 ? ($bookedDays / $totalDays) * 100 : 0;
        
        return [
            'total_days' => $totalDays,
            'available_days' => $availableDays,
            'booked_days' => $bookedDays,
            'occupancy_rate' => round($occupancyRate, 2)
        ];
    }
}
