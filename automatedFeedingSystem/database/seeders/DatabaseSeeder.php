<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\FeedModel;
use App\Models\WaterReading;
use App\Models\WaterModel;
use App\Models\FeedReading;
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

        FeedModel::factory(1)->create();
        WaterModel::factory(1)->create();

        FeedReading::factory(50)->create();
        WaterReading::factory(50)->create();
    }
}
