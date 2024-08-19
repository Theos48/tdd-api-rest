<?php

namespace Tests\Feature\Restaurants;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestaurantListTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }

    #[Test]
    public function an_authenticated_user_can_view_your_restaurants() {

        $user = User::find(2);
        Restaurant::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants");

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure(['message', 'data' => [['id', 'name', 'slug', 'description']], 'status_code']);
    }

    #[Test]
    public function an_authenticated_user_must_see_only_their_restaurants() {
        $user = User::find(2);
        Restaurant::factory()->count(10)->create([
            'user_id' => 1,
        ]);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants");

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    #[Test]
    public function an_unauthenticated_user_cannot_see_restaurants() {
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants");
        $response->assertStatus(401);
    }


}
