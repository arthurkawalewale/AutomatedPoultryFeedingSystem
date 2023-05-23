<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reading>
 */
class ReadingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reading' => $this->faker->randomFloat(2, 14, 16),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'reservoir_id' => $this->faker->numberBetween(1, 4)
        ];
    }
}
