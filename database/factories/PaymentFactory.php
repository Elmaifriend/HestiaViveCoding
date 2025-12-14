<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Enums\PaymentStatus;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'amount' => fake()->randomFloat(2, 100, 5000),
            'status' => fake()->randomElement(PaymentStatus::class),
            'date_paid' => fake()->dateTimeThisYear(),
        ];
    }
}
