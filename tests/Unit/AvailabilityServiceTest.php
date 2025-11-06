<?php

use App\Models\Property;
use App\Models\Booking;
use App\Models\User;
use App\Models\Category;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->availabilityService = new AvailabilityService();
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
    $this->property = Property::factory()->create([
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id,
        'max_guests' => 4
    ]);
});

test('check availability returns true when no conflicting bookings', function () {
    $startDate = Carbon::now()->addDays(1);
    $endDate = Carbon::now()->addDays(3);
    
    $isAvailable = $this->availabilityService->checkAvailability($this->property, $startDate, $endDate);
    
    expect($isAvailable)->toBeTrue();
});

test('check availability returns false when conflicting booking exists', function () {
    $startDate = Carbon::now()->addDays(1);
    $endDate = Carbon::now()->addDays(3);
    
    // Create a conflicting booking
    Booking::factory()->create([
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id,
        'start_date' => $startDate->copy()->addDay(),
        'end_date' => $endDate->copy()->subDay(),
        'status' => 'CONFIRME'
    ]);
    
    $isAvailable = $this->availabilityService->checkAvailability($this->property, $startDate, $endDate);
    
    expect($isAvailable)->toBeFalse();
});

test('check capacity returns true when guests within limit', function () {
    $guests = 3;
    
    $canAccommodate = $this->availabilityService->checkCapacity($this->property, $guests);
    
    expect($canAccommodate)->toBeTrue();
});

test('check capacity returns false when guests exceed limit', function () {
    $guests = 6; // More than max_guests (4)
    
    $canAccommodate = $this->availabilityService->checkCapacity($this->property, $guests);
    
    expect($canAccommodate)->toBeFalse();
});

test('get availability stats returns correct data', function () {
    $startDate = Carbon::now()->addDays(1);
    $endDate = Carbon::now()->addDays(5);
    
    // Create some bookings
    Booking::factory()->create([
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id,
        'start_date' => $startDate->copy()->addDay(),
        'end_date' => $startDate->copy()->addDays(2),
        'status' => 'CONFIRME'
    ]);
    
    $stats = $this->availabilityService->getAvailabilityStats($this->property, $startDate, $endDate);
    
    expect($stats)->toHaveKeys(['total_days', 'available_days', 'booked_days', 'occupancy_rate']);
    expect($stats['total_days'])->toBe(5);
    expect($stats['booked_days'])->toBeGreaterThan(0);
    expect($stats['occupancy_rate'])->toBeGreaterThan(0);
});

test('get available slots returns correct structure', function () {
    $startDate = Carbon::now()->addDays(1);
    $endDate = Carbon::now()->addDays(3);
    
    $slots = $this->availabilityService->getAvailableSlots($this->property, $startDate, $endDate);
    
    expect($slots)->toBeArray();
    expect($slots)->toHaveCount(3); // 3 days
    
    foreach ($slots as $slot) {
        expect($slot)->toHaveKeys(['date', 'available', 'bookings']);
        expect($slot['available'])->toBeBool();
        expect($slot['bookings'])->toBeArray();
    }
});
