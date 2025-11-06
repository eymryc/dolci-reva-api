<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Amenity;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques hôtels
        $hotels = Hotel::factory()->count(5)->create();

        foreach ($hotels as $hotel) {
            // Créer des chambres pour chaque hôtel
            HotelRoom::factory()->count(rand(10, 30))->create([
                'hotel_id' => $hotel->id,
            ]);

            // Attacher des équipements aléatoires
            $amenities = Amenity::inRandomOrder()->limit(rand(5, 10))->get();
            $hotel->amenities()->attach($amenities);
        }
    }
}
