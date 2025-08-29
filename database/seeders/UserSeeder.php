<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'phone'      => '0612345678',
                'email'      => 'john@example.com',
                'password'   => Hash::make('password123'),
                'type'       => 'SUPER_ADMIN',
            ],
            [
                'first_name' => 'Jane',
                'last_name'  => 'Smith',
                'phone'      => '0698765432',
                'email'      => 'wangny.ouangni@gmail.com',
                'password'   => Hash::make('Bonjour@2022'),
                'type'       => 'CUSTOMER',
                'services'   => [1, 2, 3], // IDs de catégories
            ],
            [
                'first_name' => 'Paul',
                'last_name'  => 'Martin',
                'phone'      => '0654321987',
                'email'      => 'paul@example.com',
                'password'   => Hash::make('password123'),
                'type'       => 'ADMIN',
            ],
        ];

        foreach ($users as $userData) {
            // On sépare les services si présents
            $services = $userData['services'] ?? [];

            unset($userData['services']); // on retire 'services' pour éviter erreur SQL

            // Création du user
            $user = User::create($userData);

            // Attacher les catégories si définies
            if (!empty($services)) {
                $user->categories()->attach($services);
            }
        }
    }
}
