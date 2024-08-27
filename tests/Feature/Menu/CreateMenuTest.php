<?php

namespace Tests\Feature\Menu;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateMenuTest extends TestCase {
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;
    protected Plate $plate;

    protected function setUp(): void {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plates = Plate::factory()->count(15)->create(['restaurant_id' => $this->restaurant->id]);
    }

    #[Test]
    public function an_authenticated_user_can_create_a_menu() {
        $data = [
            'name' => 'menu name',
            'description' => 'menu description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        $this->actingAs($this->user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'plates' => [['name', 'description', 'price']]
            ],
            'status_code'
        ]);

        $firstPlate = $this->plates->first();

        $response->assertJsonPath('data.plates.0', [
            'name' => $firstPlate->name,
            'description' => $firstPlate->description,
            'price' => (string)$firstPlate->price,
        ]);

        $this->assertDatabaseHas('menus', [
            'restaurant_id' => $this->restaurant->id,
            'name' => $data['name'],
            'description' => $data['description'],
        ]);


        foreach ($this->plates as $plate) {
            {
                $this->assertDatabaseHas('menus_plates', [
                    'menu_id' => 1,
                    'plate_id' => $plate->id,
                ]);
            }
        }
    }

    #[Test]
    public function an_unauthenticated_user_cannot_create_a_plate() {
        $data = [
            'name' => 'menu name',
            'description' => 'menu description',
            'plate_ids' => $this->plates->pluck('id')
        ];
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertStatus(401);
    }

    #[Test]
    public function menu_name_must_be_required() {
        $data = [
            'name' => '',
            'description' => 'menu description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        $this->actingAs($this->user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['name']]);
    }

    #[Test]
    public function menu_description_must_be_required() {
        $data = [
            'name' => 'menu name',
            'description' => '',
            'plate_ids' => $this->plates->pluck('id')
        ];

        $this->actingAs($this->user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['description']]);
    }

    #[Test]
    public function plate_ids_must_be_required() {
        $data = [
            'name' => 'menu name',
            'description' => 'menu description',
            'plate_ids' => []
        ];

        $this->actingAs($this->user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/menus", $data);;

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['plate_ids']]);
    }

    #[Test]
    public function plate_ids_must_existing() {
        $data = [
            'name' => 'menu name',
            'description' => 'menu description',
            'plate_ids' => [101]
        ];

        $this->actingAs($this->user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['plate_ids']]);

    }

    #[Test]
    public function restaurant_must_belongs_to_user() {
        $data = [
            'name' => 'menu name',
            'description' => 'menu description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        $this->actingAs(User::factory()->create());
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertStatus(403);
    }

    #[Test]
    public function menu_plates_must_belongs_to_user() {
        $plate = Plate::factory()->create();

        $data = [
            'name' => 'menu name',
            'description' => 'menu description',
            'plate_ids' => [$plate->id]
        ];

        $this->actingAs($this->user);
        $response = $this->postJson("{$this->apiBaseUrl}/restaurants/{$this->restaurant->id}/menus", $data);;

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['plate_ids']]);
    }
}
