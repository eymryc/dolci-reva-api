<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\User;
use App\Models\Category;
use App\Models\Amenity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyAmenitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_amenities_return_id_and_name()
    {
        // Créer un utilisateur, une catégorie et une propriété
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $property = Property::factory()->create([
            'owner_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // Créer des amenities avec plusieurs champs
        $amenity1 = Amenity::factory()->create([
            'name' => 'WiFi',
        ]);

        $amenity2 = Amenity::factory()->create([
            'name' => 'Piscine',
        ]);

        // Attacher les amenities à la propriété
        $property->amenities()->attach([$amenity1->id, $amenity2->id]);

        // Récupérer la propriété avec ses amenities
        $propertyWithAmenities = Property::with('amenities')->find($property->id);

        // Vérifier que les amenities ont id et name
        $this->assertCount(2, $propertyWithAmenities->amenities);
        
        $amenityData = $propertyWithAmenities->amenities->first()->toArray();
        $this->assertArrayHasKey('id', $amenityData);
        $this->assertArrayHasKey('name', $amenityData);
        $this->assertEquals('WiFi', $amenityData['name']);
    }

    public function test_property_amenities_structure()
    {
        // Créer un utilisateur, une catégorie et une propriété
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $property = Property::factory()->create([
            'owner_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // Créer une amenity
        $amenity = Amenity::factory()->create(['name' => 'Piscine']);

        // Attacher l'amenity à la propriété
        $property->amenities()->attach($amenity->id);

        // Récupérer la propriété avec ses amenities
        $propertyWithAmenities = Property::with('amenities')->find($property->id);

        // Vérifier la structure des amenities
        $this->assertCount(1, $propertyWithAmenities->amenities);
        
        $amenityData = $propertyWithAmenities->amenities->first();
        $this->assertEquals($amenity->id, $amenityData->id);
        $this->assertEquals('Piscine', $amenityData->name);
    }
}
