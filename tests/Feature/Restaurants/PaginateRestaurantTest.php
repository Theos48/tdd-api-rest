<?php

namespace Tests\Feature\Restaurants;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaginateRestaurantTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(RestaurantSeeder::class);
    }

    #[Test]
    public function an_authenticated_user_can_view_your_restaurants() {

        $user = User::find(2);
        Restaurant::factory()->count(150)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAsUser($user);
        $response = $this->getJson("{$this->apiBaseUrl}/restaurants");

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'data' => [
                'list' => [['id', 'name', 'slug', 'description']],
                'total',
                'current_page',
                'per_page',
                'total_pages',
                'count'],
            'status_code']);
        $response->assertJsonCount(15, 'data.list');
        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('data.total', 150)
            ->where('data.current_page', 1)
            ->where('data.per_page', 15)
            ->where('data.total_pages', 10)
            ->where('data.count', 15)
            ->etc());

    }
}
