<?php

namespace Database\Factories;

use App\Models\WaterTank;
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
            'trough_reading' => $this->faker->randomFloat(2, 0, 16),
            'reservoir_reading' => $this->faker->randomFloat(2, 0, 16),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'water_model_id' => 1,
        ];
    }
}
