<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class PricingService
{
    /**
     * Calculer le prix total d'une réservation
     */
    public function calculateTotalPrice($bookable, Carbon $startDate, Carbon $endDate, int $guests = 1)
    {
        $basePrice = $this->getBasePrice($bookable);
        $duration = $this->calculateDuration($bookable, $startDate, $endDate);
        
        $totalPrice = $basePrice * $duration;
        
        // Appliquer les frais de service si nécessaire
        $serviceFee = $this->calculateServiceFee($totalPrice);
        
        return [
            'base_price' => $basePrice,
            'duration' => $duration,
            'subtotal' => $totalPrice,
            'service_fee' => $serviceFee,
            'total_price' => $totalPrice + $serviceFee,
            'guests' => $guests
        ];
    }
    
    /**
     * Calculer les commissions
     */
    public function calculateCommissions($totalPrice, $commissionRate = 0.1)
    {
        $commissionAmount = $totalPrice * $commissionRate;
        $ownerAmount = $totalPrice - $commissionAmount;
        
        return [
            'total_price' => $totalPrice,
            'commission_rate' => $commissionRate,
            'commission_amount' => round($commissionAmount, 2),
            'owner_amount' => round($ownerAmount, 2)
        ];
    }
    
    /**
     * Calculer le prix avec remises
     */
    public function calculatePriceWithDiscounts($bookable, Carbon $startDate, Carbon $endDate, array $discounts = [])
    {
        $pricing = $this->calculateTotalPrice($bookable, $startDate, $endDate);
        $totalDiscount = 0;
        
        foreach ($discounts as $discount) {
            if ($discount['type'] === 'percentage') {
                $discountAmount = $pricing['total_price'] * ($discount['value'] / 100);
            } else {
                $discountAmount = $discount['value'];
            }
            
            $totalDiscount += $discountAmount;
        }
        
        $finalPrice = max(0, $pricing['total_price'] - $totalDiscount);
        
        return array_merge($pricing, [
            'discounts' => $discounts,
            'total_discount' => $totalDiscount,
            'final_price' => $finalPrice
        ]);
    }
    
    /**
     * Obtenir le prix de base selon le type d'élément
     */
    private function getBasePrice($bookable)
    {
        if (method_exists($bookable, 'price')) {
            return $bookable->price;
        }
        
        if (method_exists($bookable, 'price_per_person')) {
            return $bookable->price_per_person;
        }
        
        return 0;
    }
    
    /**
     * Calculer la durée selon le type d'élément
     */
    private function calculateDuration($bookable, Carbon $startDate, Carbon $endDate)
    {
        $className = class_basename($bookable);
        
        switch ($className) {
            case 'Residence':
            case 'HotelRoom':
                // Pour les hébergements, calculer en nuits
                return $startDate->diffInDays($endDate);
                
                
            default:
                // Par défaut, calculer en jours
                return $startDate->diffInDays($endDate);
        }
    }
    
    /**
     * Calculer les frais de service
     */
    private function calculateServiceFee($totalPrice)
    {
        // Frais de service de 5% avec un minimum de 1€ et maximum de 50€
        $serviceFeeRate = 0.05;
        $serviceFee = $totalPrice * $serviceFeeRate;
        
        return max(1, min(50, $serviceFee));
    }
    
    /**
     * Obtenir les tarifs par période
     */
    public function getPricingByPeriod($bookable, Carbon $startDate, Carbon $endDate)
    {
        $pricing = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lt($endDate)) {
            $nextDate = $currentDate->copy()->addDay();
            
            $pricing[] = [
                'date' => $currentDate->format('Y-m-d'),
                'price' => $this->getBasePrice($bookable),
                'available' => true // À implémenter avec AvailabilityService
            ];
            
            $currentDate = $nextDate;
        }
        
        return $pricing;
    }
}
