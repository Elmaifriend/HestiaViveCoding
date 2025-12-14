<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'resident',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
            ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'usuario@ejemplo.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'usuario@ejemplo.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user',
            ]);
    }

    public function test_user_can_get_profile()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        
        // Sanctum requires the user to be authenticated to logout usually, 
        // actingAs sets the current user but for logout usually we need a valid token 
        // if the controller revokes it. 
        // However, standard actingAs works for endpoint protection.
        
        $response = $this->actingAs($user)->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }
}
