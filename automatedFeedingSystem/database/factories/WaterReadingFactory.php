<?php

namespace Database\Factories;

use App\Models\WaterReading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WaterReading>
 */
class WaterReadingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trough_reading' => fake()->randomFloat(2, 0, 16),
            'reservoir_reading' => fake()->randomFloat(2, 0, 16),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'number_of_birds' => fake()->numberBetween('0','20'),
            'water_model_id' => 1,
        ];
    }
}
