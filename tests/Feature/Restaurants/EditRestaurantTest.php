<?php

namespace Tests\Feature\Restaurants;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditRestaurantTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }

    #[Test]
    public function an_authenticated_user_can_edit_their_restaurant() {
        $data = [
            'name' => 'Edit restaurant',
            'description' => 'Edit restaurant description',
        ];

        $user = User::find(1);
        $restaurant = Restaurant::find(1);

        $this->actingAsUser($user);
        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}/edit", $data);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'data' => [
            'id', 'name', 'slug', 'description'
        ], 'status_code']);
        $restaurant->refresh();
        $this->assertStringContainsString('edit-restaurant', $restaurant->slug);
        $this->assertDatabaseHas('restaurants', [
            'name' => $data['name'],
            'slug' => $restaurant->slug,
            'description' => $data['description'],
        ]);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_edit_their_restaurant() {
        $data = [
            'name' => 'Edit restaurant',
            'description' => 'Edit restaurant description',
        ];

        $restaurant = Restaurant::find(1);

        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}/edit", $data);
        $response->assertStatus(401);
    }

    #[Test]
    public function a_user_cannot_update_restaurants_other_than_his_own() {
        $data = [
            'name' => 'Edit restaurant',
            'description' => 'Edit restaurant description',
        ];

        $user = User::find(2);
        $restaurant = Restaurant::find(1);

        $this->actingAsUser($user);
        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}/edit", $data);

        $response->assertStatus(403);
    }

     #[Test]
    public function name_must_be_required() {
      $data = [
            'name' => '',
            'description' => 'Edit restaurant description',
        ];

        $user = User::find(1);
        $restaurant = Restaurant::find(1);

        $this->actingAsUser($user);
        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}/edit", $data);


        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['name']]);
    }

    #[Test]
    public function description_must_be_required() {
      $data = [
            'name' => 'Edit restaurant',
            'description' => '',
        ];

        $user = User::find(1);
        $restaurant = Restaurant::find(1);

        $this->actingAsUser($user);
        $response = $this->putJson("{$this->apiBaseUrl}/restaurants/{$restaurant->id}/edit", $data);


        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['description']]);
    }

}
