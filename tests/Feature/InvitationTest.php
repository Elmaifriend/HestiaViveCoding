<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_invitations()
    {
        $user = User::factory()->create();
        Invitation::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/invitations');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_create_invitation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/invitations', [
            'expiration_hours' => 24,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'qr_code',
                'status',
                'expiration_date',
            ]);
    }

    public function test_validate_qr_code()
    {
        $user = User::factory()->create();
        $invitation = Invitation::factory()->create(['user_id' => $user->id, 'status' => 'active']);

        $response = $this->actingAs($user)->postJson('/api/invitations/validate', [
            'qr_code' => $invitation->qr_code,
            'mark_used' => true,
        ]);

        $response->assertStatus(200)
             ->assertJson(['message' => 'Valid QR.']);
             
        $this->assertDatabaseHas('invitations', [
            'id' => $invitation->id,
            'status' => 'used', 
        ]);
    }
    
    public function test_user_can_list_gate_access()
    {
        $user = User::factory()->create();
        // Assuming GateAccess creates records linked to user or building? 
        // Based on endpoint usually it lists history.
        
        $response = $this->actingAs($user)->getJson('/api/gate-access');
        
        $response->assertStatus(200);
    }

    public function test_user_can_register_manual_gate_access()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->postJson('/api/gate-access', [
            'guest_name' => 'Chofer Uber',
            'entry_date' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(201); // Or 200 depending on implementation
    }
}
