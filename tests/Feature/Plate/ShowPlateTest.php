<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowPlateTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
        $this->restaurant = Restaurant::find(1);
        $this->plate = Plate::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'name' => 'plate name',
            'description' => 'plate description',
            'price' => '$10',
        ]);
    }

    #[Test]
    public function an_authenticated_user_must_see_one_of_their_restaurants() {
        $user = User::find(1);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id', 'restaurant_id', 'name', 'description', 'price'
            ],
            'status_code'
        ]);
        $response->assertJsonFragment([
            'data' => [
                'id' => $this->plate->id,
                'restaurant_id' => $this->plate->restaurant_id,
                'name' => $this->plate->name,
                'description' => $this->plate->description,
                'price' => $this->plate->price,
            ]
        ]);
    }

    #[Test]
    public function an_authenticated_user_must_see_only_their_restaurants() {
        $user = User::find(2);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function a_unauthenticated_user_cannot_see_any_restaurant() {
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(401);
    }

}
