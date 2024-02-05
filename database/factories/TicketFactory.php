<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(5, true),
            'subtitle' => fake()->words(4, true),
            'time_to_use' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'price' => fake()->numberBetween(100, 999),
            'image' => null,
            'created_at' => fake()->dateTimeBetween(),
        ];
    }
}
