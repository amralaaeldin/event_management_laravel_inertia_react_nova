<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class EventFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'description' => fake()->sentence(10),
            'start_date_time' => fake()->dateTimeBetween('+1 days', '+1 month'),
            'location' => fake()->city() . ', ' . fake()->country,
            'status' => fake()->randomElement(['draft', 'published']),
            'capacity' => fake()->numberBetween(10, 50),
            'waitlist_capacity' => fake()->numberBetween(5, 30),
            'duration' => fake()->numberBetween(30, 240), // in minutes
        ];
    }
}
