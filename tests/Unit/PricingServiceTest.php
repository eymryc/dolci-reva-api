<?php

use App\Models\Property;
use App\Models\User;
use App\Models\Category;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->pricingService = new PricingService();
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
    $this->property = Property::factory()->create([
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id,
        'price' => 100.00
    ]);
});

test('calculate total price for property', function () {
    $startDate = Carbon::now()->addDays(1);
    $endDate = Carbon::now()->addDays(3);
    $guests = 2;
    
    $pricing = $this->pricingService->calculateTotalPrice($this->property, $startDate, $endDate, $guests);
    
    expect($pricing)->toHaveKeys([
        'base_price',
        'duration',
        'subtotal',
        'service_fee',
        'total_price',
        'guests'
    ]);
    
    expect($pricing['base_price'])->toBe(100.00);
    expect($pricing['duration'])->toBe(2); // 2 nights
    expect($pricing['subtotal'])->toBe(200.00); // 100 * 2
    expect($pricing['guests'])->toBe(2);
    expect($pricing['total_price'])->toBeGreaterThan($pricing['subtotal']); // Includes service fee
});

test('calculate commissions correctly', function () {
    $totalPrice = 200.00;
    $commissionRate = 0.1; // 10%
    
    $commissions = $this->pricingService->calculateCommissions($totalPrice, $commissionRate);
    
    expect($commissions)->toHaveKeys([
        'total_price',
        'commission_rate',
        'commission_amount',
        'owner_amount'
    ]);
    
    expect($commissions['total_price'])->toBe(200.00);
    expect($commissions['commission_rate'])->toBe(0.1);
    expect($commissions['commission_amount'])->toBe(20.00);
    expect($commissions['owner_amount'])->toBe(180.00);
});

test('calculate price with discounts', function () {
    $startDate = Carbon::now()->addDays(1);
    $endDate = Carbon::now()->addDays(2);
    
    $discounts = [
        ['type' => 'percentage', 'value' => 10], // 10% discount
        ['type' => 'fixed', 'value' => 20] // 20€ discount
    ];
    
    $pricing = $this->pricingService->calculatePriceWithDiscounts($this->property, $startDate, $endDate, $discounts);
    
    expect($pricing)->toHaveKeys([
        'discounts',
        'total_discount',
        'final_price'
    ]);
    
    expect($pricing['discounts'])->toBe($discounts);
    expect($pricing['total_discount'])->toBeGreaterThan(0);
    expect($pricing['final_price'])->toBeLessThan($pricing['total_price']);
});

test('get pricing by period returns correct structure', function () {
    $startDate = Carbon::now()->addDays(1);
    $endDate = Carbon::now()->addDays(3);
    
    $pricing = $this->pricingService->getPricingByPeriod($this->property, $startDate, $endDate);
    
    expect($pricing)->toBeArray();
    expect($pricing)->toHaveCount(2); // 2 days
    
    foreach ($pricing as $dayPricing) {
        expect($dayPricing)->toHaveKeys(['date', 'price', 'available']);
        expect($dayPricing['price'])->toBe(100.00);
        expect($dayPricing['available'])->toBeTrue();
    }
});

test('service fee calculation respects min and max limits', function () {
    // Test minimum service fee
    $startDate = Carbon::now()->addDays(1);
    $endDate = Carbon::now()->addDays(2);
    
    // Create property with very low price
    $lowPriceProperty = Property::factory()->create([
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id,
        'price' => 5.00 // Very low price
    ]);
    
    $pricing = $this->pricingService->calculateTotalPrice($lowPriceProperty, $startDate, $endDate);
    
    // Service fee should be at least 1€
    expect($pricing['service_fee'])->toBeGreaterThanOrEqual(1);
    
    // Test maximum service fee
    $highPriceProperty = Property::factory()->create([
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id,
        'price' => 2000.00 // High price
    ]);
    
    $pricing = $this->pricingService->calculateTotalPrice($highPriceProperty, $startDate, $endDate);
    
    // Service fee should be at most 50€
    expect($pricing['service_fee'])->toBeLessThanOrEqual(50);
});
