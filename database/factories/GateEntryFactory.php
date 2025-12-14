<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Enums\GateEntryStatus;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GateEntry>
 */
class GateEntryFactory extends Factory
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
            'guest_name' => fake()->name(),
            'entry_date' => fake()->dateTimeBetween('now', '+1 week'),
            'status' => fake()->randomElement(GateEntryStatus::class),
        ];
    }
}
