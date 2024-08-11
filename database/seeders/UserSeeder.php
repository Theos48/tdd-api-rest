<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        User::factory()->createMany([
            [
                'name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@doe.com',
            ],
            [
                'name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane@doe.com',
            ],
        ]);
    }
}
