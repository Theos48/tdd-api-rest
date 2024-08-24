<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlateListTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
        $this->restaurant = Restaurant::find(1);
        $this->plates = Plate::factory()->count(15)->create([
            'restaurant_id' => $this->restaurant,
        ]);
    }

    #[Test]
    public function an_authenticated_user_must_see_their_plates() {
        $user = User::find(1);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates");

        $response->assertOk();
        $response->assertJsonCount(15, 'data.list');
        $response->assertJsonStructure([
            'message',
            'data' => [
                'list' => [['id', 'restaurant_id', 'name', 'price', 'description']]],
            'status_code']);

        foreach (range(0, 14) as $platePosition) {
            $response->assertJsonPath("data.list.{$platePosition}.restaurant_id", $this->restaurant->id);
        }
    }

    #[Test]
    public function a_user_must_see_their_paginated_plates(): void {
        $user = User::find(1);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates");

        $response->assertOk();
        $response->assertJsonCount(15, 'data.list');
        $response->assertJsonStructure([
            'message',
            'data' => [
                'list' => [['id', 'restaurant_id', 'name', 'price', 'description']],
                'total',
                'current_page',
                'per_page',
                'total_pages',
                'count'],
            'status_code']);

        $response->assertJsonPath('data.total', 15);
        $response->assertJsonPath('data.current_page', 1);
        $response->assertJsonPath('data.per_page', 15);
        $response->assertJsonPath('data.total_pages', 1);
        $response->assertJsonPath('data.count', 15);
    }


    #[Test]
    public function an_authenticated_user_must_see_only_their_restaurants() {
        $user = User::find(2);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates");

        $response->assertDontSee(403);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_see_restaurants() {
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/plates");
        $response->assertStatus(401);
    }

}
