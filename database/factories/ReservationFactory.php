<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Enums\ReservationStatus;
use App\Models\Amenity;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
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
            'amenity_id' => Amenity::factory(),
            'date' => fake()->dateTimeBetween('now', '+1 month'),
            'status' => fake()->randomElement(ReservationStatus::class),
        ];
    }
}
