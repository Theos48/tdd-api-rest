<?php

namespace Tests\Feature\Restaurants;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteRestaurantTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }

    #[Test]
    public function an_authenticated_user_can_delete_their_restaurants() {
        $user = User::find(1);
        $restaurant = Restaurant::find(1);

        $this->actingAsUser($user);
        $response = $this->deleteJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}/delete");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('restaurants', [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
        ]);
        $response->assertJsonStructure(['message', 'data', 'status_code']);
    }

    #[Test]
    public function an_authenticated_user_must_delete_only_their_restaurants() {
        $user = User::find(2);
        $restaurant = Restaurant::find(1);

        $this->actingAsUser($user);
        $response = $this->deleteJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}/delete");

        $response->assertStatus(403);
        $this->assertDatabaseCount('restaurants', 2);
        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'slug' => $restaurant->slug,
            'description' => $restaurant->description,
        ]);
    }


    #[Test]
    public function an_unauthenticated_user_cannot_delete_restaurants() {
        $restaurant = Restaurant::find(1);

        $response = $this->deleteJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}/delete");

        $response->assertStatus(401);
        $this->assertDatabaseCount('restaurants', 2);
    }
}
