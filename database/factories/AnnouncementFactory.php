<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'type' => fake()->randomElement(AnnouncementType::class),
            'status' => fake()->randomElement(AnnouncementStatus::class),
            'send_push' => fake()->boolean(30),
        ];
    }
}
