<?php

use App\Models\User;
use App\Models\Property;
use App\Models\Category;
use App\Models\Amenity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['type' => 'OWNER']);
    $this->category = Category::factory()->create();
    $this->amenity = Amenity::factory()->create();
});

test('can create property with valid data', function () {
    $propertyData = [
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id,
        'title' => 'Beautiful Villa',
        'description' => 'A beautiful villa with amazing views and modern amenities. Perfect for families and groups looking for a luxurious stay.',
        'address' => '123 Main Street, Beautiful City',
        'state' => 'Beautiful State',
        'city' => 'Beautiful City',
        'country' => 'Beautiful Country',
        'latitude' => 40.7128,
        'longitude' => -74.0060,
        'max_guests' => 6,
        'bedrooms' => 3,
        'bathrooms' => 2,
        'price' => 150.00,
        'type' => 'VILLA',
        'rental_type' => 'ENTIER',
        'amenities' => [$this->amenity->id]
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/properties', $propertyData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'price',
                'max_guests',
                'type',
                'rental_type'
            ]
        ]);

    $this->assertDatabaseHas('properties', [
        'title' => 'Beautiful Villa',
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id
    ]);
});

test('cannot create property with invalid data', function () {
    $propertyData = [
        'title' => 'A', // Too short
        'description' => 'Short', // Too short
        'price' => -10, // Negative price
        'max_guests' => 0, // Invalid guests
        'latitude' => 200, // Invalid latitude
        'longitude' => 200, // Invalid longitude
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/properties', $propertyData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'title',
            'description',
            'price',
            'max_guests',
            'latitude',
            'longitude',
            'category_id',
            'address',
            'city',
            'country',
            'type',
            'rental_type'
        ]);
});

test('can get property by id', function () {
    $property = Property::factory()->create([
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id
    ]);

    $response = $this->actingAs($this->user)
        ->getJson("/api/properties/{$property->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'title',
            'description',
            'price',
            'max_guests',
            'type',
            'rental_type',
            'owner',
            'category'
        ]);
});

test('can update property', function () {
    $property = Property::factory()->create([
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id,
        'title' => 'Original Title'
    ]);

    $updateData = [
        'title' => 'Updated Title',
        'description' => 'Updated description with more details about this beautiful property.',
        'price' => 200.00
    ];

    $response = $this->actingAs($this->user)
        ->putJson("/api/properties/{$property->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'success' => true,
            'message' => 'Property updated successfully'
        ]);

    $this->assertDatabaseHas('properties', [
        'id' => $property->id,
        'title' => 'Updated Title',
        'price' => 200.00
    ]);
});

test('can delete property', function () {
    $property = Property::factory()->create([
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id
    ]);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/properties/{$property->id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 200,
            'success' => true,
            'message' => 'Deleted successfully'
        ]);

    $this->assertSoftDeleted('properties', [
        'id' => $property->id
    ]);
});

test('can get all properties', function () {
    Property::factory()->count(5)->create([
        'owner_id' => $this->user->id,
        'category_id' => $this->category->id
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/properties');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});
