<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Restaurant::factory()->createMany([
            [
                'user_id' => 1,
                'name' => 'Restaurant 1',
                'slug' => 'restaurant-1',
                'description' => 'Restaurant 1 description',
            ],
            [
                'user_id' => 1,
                'name' => 'Restaurant 2',
                'slug' => 'restaurant-2',
                'description' => 'Restaurant 2 description',
            ],
        ]);
    }
}
