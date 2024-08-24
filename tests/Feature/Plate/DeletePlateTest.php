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

class DeletePlateTest extends TestCase {
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
    public function an_authenticated_user_can_delete_their_plates() {
        $user = User::find(1);
        $this->actingAs($user);
        $response = $this->delete("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('plates', [
            'id' => $this->plate->id,
            'restaurant_id' => $this->restaurant->id,
            ...$this->plate->attributesToArray(),
        ]);

        $response->assertJsonStructure(['message', 'data', 'status_code']);
    }

    #[Test]
    public function an_authenticated_user_must_delete_only_their_restaurants() {
        $user = User::find(2);
        $this->actingAs($user);
        $response = $this->delete("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_delete_restaurants() {
        $restaurant = Restaurant::find(1);
        $response = $this->deleteJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(401);
    }
}
