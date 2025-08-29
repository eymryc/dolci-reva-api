<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'HÔTEL'],
            ['name' => 'RÉSIDENCE'],
            ['name' => 'BAR'],
            ['name' => 'LOUNGE'],
            ['name' => 'NIGHT CLUB'],
            ['name' => 'RESTAURANT'],
            ['name' => 'ESPACE ÉVÈNEMENTIEL'],
            ['name' => 'ESPACE DE JEU'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
