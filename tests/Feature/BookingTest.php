<?php

use App\Models\User;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['type' => 'CUSTOMER']);
    $this->owner = User::factory()->create(['type' => 'OWNER']);
    $this->category = Category::factory()->create();
    $this->property = Property::factory()->create([
        'owner_id' => $this->owner->id,
        'category_id' => $this->category->id,
        'price' => 100.00,
        'max_guests' => 4
    ]);
});

test('can create booking with valid data', function () {
    $bookingData = [
        'customer_id' => $this->user->id,
        'owner_id' => $this->owner->id,
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id,
        'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
        'total_price' => 200.00,
        'status' => 'EN_ATTENTE',
        'payment_status' => 'EN_ATTENTE'
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bookings', $bookingData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'success',
            'message',
            'data' => [
                'id',
                'customer_id',
                'owner_id',
                'bookable_type',
                'bookable_id',
                'start_date',
                'end_date',
                'total_price',
                'status',
                'payment_status'
            ]
        ]);

    $this->assertDatabaseHas('bookings', [
        'customer_id' => $this->user->id,
        'owner_id' => $this->owner->id,
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id
    ]);
});

test('cannot create booking with invalid dates', function () {
    $bookingData = [
        'customer_id' => $this->user->id,
        'owner_id' => $this->owner->id,
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id,
        'start_date' => now()->subDays(1)->format('Y-m-d H:i:s'), // Date passée
        'end_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        'total_price' => 200.00
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bookings', $bookingData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['start_date']);
});

test('cannot create booking with end date before start date', function () {
    $bookingData = [
        'customer_id' => $this->user->id,
        'owner_id' => $this->owner->id,
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id,
        'start_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(1)->format('Y-m-d H:i:s'), // End before start
        'total_price' => 200.00
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bookings', $bookingData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['end_date']);
});

test('can get booking by id', function () {
    $booking = Booking::factory()->create([
        'customer_id' => $this->user->id,
        'owner_id' => $this->owner->id,
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id
    ]);

    $response = $this->actingAs($this->user)
        ->getJson("/api/bookings/{$booking->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'customer_id',
            'owner_id',
            'bookable_type',
            'bookable_id',
            'start_date',
            'end_date',
            'total_price'
        ]);
});

test('can update booking', function () {
    $booking = Booking::factory()->create([
        'customer_id' => $this->user->id,
        'owner_id' => $this->owner->id,
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id,
        'status' => 'EN_ATTENTE'
    ]);

    $updateData = [
        'status' => 'CONFIRME',
        'payment_status' => 'PAYE'
    ];

    $response = $this->actingAs($this->user)
        ->putJson("/api/bookings/{$booking->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'success' => true,
            'message' => 'Booking updated successfully'
        ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'CONFIRME',
        'payment_status' => 'PAYE'
    ]);
});

test('can delete booking', function () {
    $booking = Booking::factory()->create([
        'customer_id' => $this->user->id,
        'owner_id' => $this->owner->id,
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id
    ]);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/bookings/{$booking->id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'success' => true,
            'message' => 'Deleted successfully'
        ]);

    $this->assertSoftDeleted('bookings', [
        'id' => $booking->id
    ]);
});

test('can get all bookings', function () {
    Booking::factory()->count(3)->create([
        'customer_id' => $this->user->id,
        'owner_id' => $this->owner->id,
        'bookable_type' => 'Property',
        'bookable_id' => $this->property->id
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/bookings');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});
