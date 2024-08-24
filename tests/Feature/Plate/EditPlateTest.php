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

class EditPlateTest extends TestCase {
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
    public function an_authenticated_user_can_edit_plate() {
        $this->withoutExceptionHandling();
        $data = [
            'name' => 'new plate name',
            'description' => 'new plate description',
            'price' => '$15',
        ];

        $user = User::find(1);
        $this->actingAs($user);
        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

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
                ...$data,
                'id' => $this->plate->id,
                'restaurant_id' => $this->restaurant->id,
            ]
        ]);

        $this->assertDatabaseHas("plates", [
            'id' => $this->plate->id,
            'restaurant_id' => $this->restaurant->id,
            ...$data,
        ]);
    }

    #[Test]
    public function an_authenticated_user_can_only_update_a_plate_for_their_restaurant() {
        $data = [
            'name' => 'new plate name',
            'description' => 'new plate description',
            'price' => '$15',
        ];

        $user = User::find(2);
        $this->actingAs($user);
        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(403);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_create_a_plate() {
        $data = [
            'name' => 'new plate name',
            'description' => 'new plate description',
            'price' => '$15',
        ];

        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('plates', $data);
    }

    #[Test]
    public function plate_name_must_be_required() {
        $data = [
            'description' => 'new plate description',
            'price' => '$15',
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
            'name' => 'new plate name',
            'price' => '$15',
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
            'name' => 'new plate name',
            'description' => 'new plate description',
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
            'name' => 'new plate name',
            'description' => 'new plate description',
            'price' => '$15',
        ];

        $user = User::find(1);
        $this->actingAs($user);
        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{4002}/plates/{$this->plate->id}", $data);

        $response->assertStatus(404);
    }

}
