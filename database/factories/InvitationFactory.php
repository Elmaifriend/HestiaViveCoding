<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Enums\InvitationStatus;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resident_id' => User::factory(),
            'qr_code' => Str::upper(Str::random(12)),
            'expiration_date' => now()->addDays(1),
            'status' => fake()->randomElement(InvitationStatus::class),
        ];
    }
}
