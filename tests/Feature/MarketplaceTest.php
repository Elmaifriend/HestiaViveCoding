<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_products()
    {
        $user = User::factory()->create();
        Product::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/marketplace');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_create_product()
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $response = $this->actingAs($user)->postJson('/api/marketplace', [
            'title' => 'Bicicleta de montaña',
            'description' => 'Usada en buen estado',
            'price' => 500,
            'photo' => UploadedFile::fake()->image('bicicleta.jpg'),
        ]);

        $response->assertStatus(201);
    }
    
    public function test_show_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->getJson("/api/marketplace/{$product->id}");
        
        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'title' => $product->title,
            ]);
    }
}
