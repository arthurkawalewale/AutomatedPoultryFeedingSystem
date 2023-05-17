<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WaterReading;
use App\Models\WaterTank;
use Database\Factories\WaterTankFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        WaterTank::factory(1)->create();

        WaterReading::factory()
            ->count(50) // Generate 50 water readings
            ->create();
    }
}
