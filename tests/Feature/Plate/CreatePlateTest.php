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

class CreatePlateTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
        $this->restaurant = Restaurant::find(1);
    }

    #[Test]
    public function an_authenticated_user_can_create_a_plate() {
        $data = [
            'name' => 'name plate',
            'description' => 'description plate',
            'price' => '$10',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id', 'restaurant_id', 'name', 'description', 'price'
            ],
            'status_code'
        ]);
        $this->assertDatabaseHas('plates', $data);
    }

    #[Test]
    public function an_authenticated_user_can_only_create_a_plate_for_their_restaurant() {
        $data = [
            'name' => 'name plate',
            'description' => 'description plate',
            'price' => '$10',
        ];

        $user = User::find(2);
        $this->actingAsUser($user);

        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('plates', $data);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_create_a_plate() {
        $data = [
            'name' => 'name plate',
            'description' => 'description plate',
            'price' => '$10',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('plates', $data);
    }

    #[Test]
    public function plate_name_must_be_required() {
        $data = [
            'description' => 'description plate',
            'price' => '$10',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['name']]);
    }

    #[Test]
    public function plate_description_must_be_required() {
        $data = [
            'name' => 'name plate',
            'price' => '$10',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['description']]);
    }

    #[Test]
    public function plate_price_must_be_required() {
        $data = [
            'name' => 'name plate',
            'description' => 'description plate',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['price']]);
    }

        #[Test]
    public function restaurant_id_must_exist_in_database() {
        $data = [
            'name' => 'name plate',
            'description' => 'description plate',
            'price' => '$10',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/4004/plates", $data);

        $response->assertStatus(404);
    }

}
