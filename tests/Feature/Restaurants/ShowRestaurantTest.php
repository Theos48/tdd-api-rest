<?php

namespace Tests\Feature\Restaurants;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowRestaurantTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }

    #[Test]
    public function an_authenticated_user_must_see_one_of_their_restaurants() {
        $user = User::find(1);
        $restaurant = Restaurant::find(1);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}");

        $response->assertOk();
        $response->assertJsonStructure(['message', 'data' => ['id', 'name', 'slug', 'description'], 'status_code']);
    }

    #[Test]
    public function an_authenticated_user_must_see_only_their_restaurants() {
        $user = User::find(2);
        $restaurant = Restaurant::find(1);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function a_unauthenticated_user_cannot_see_any_restaurant() {
        $restaurant = Restaurant::find(1);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}");
        $response->assertStatus(401);
    }

}
