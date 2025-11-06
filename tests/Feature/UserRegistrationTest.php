<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register_without_services()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '0612345678',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'type' => 'CUSTOMER',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Account created successfully',
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_user_can_register_with_services()
    {
        // Créer des catégories pour le test
        $categories = Category::factory()->count(3)->create();

        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '0698765432',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'type' => 'CUSTOMER',
            'services' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Account created successfully',
                ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertCount(3, $user->categories);
    }

    public function test_user_registration_validation_without_required_fields()
    {
        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Validation errors',
                ]);
    }

    public function test_user_registration_with_invalid_services()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '0612345678',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'type' => 'CUSTOMER',
            'services' => [999, 998], // IDs inexistants
        ];

        $response = $this->postJson('/api/users', $userData);

        // La validation devrait passer car services est nullable
        // Mais les catégories ne seront pas attachées car elles n'existent pas
        $response->assertStatus(201);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertCount(0, $user->categories);
    }
}
