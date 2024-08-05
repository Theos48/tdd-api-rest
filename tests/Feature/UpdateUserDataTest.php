<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserDataTest extends TestCase {

    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    #[Test]
    public function an_authenticated_user_can_update_their_data() {
        $data = [
            'name' => 'newname',
            'last_name' => 'new lastname',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/profile", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'status_code']);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('data.name', $data['name'])
            ->where('data.last_name', $data['last_name'])
            ->missing('password')
            ->etc()
        );

        $this->assertDatabaseHas('users', [
            'name' => 'newname',
            'email' => $user->email,
            'last_name' => 'new lastname',
        ]);
    }

    #[Test]
    public function an_authenticated_user_cannot_update_their_email() {
        $data = [
            'name' => 'newname',
            'last_name' => 'new lastname',
            'email' => 'newemail@example.com',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/profile", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'status_code']);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('data.name', $data['name'])
            ->where('data.last_name', $data['last_name'])
            ->where('data.email', $user->email)
            ->missing('password')
            ->etc()
        );

        $this->assertDatabaseHas('users', [
            'name' => 'newname',
            'email' => $user->email,
            'last_name' => 'new lastname',
        ]);
    }

    #[Test]
    public function an_authenticated_user_cannot_update_their_password() {
        $data = [
            'name' => 'newname',
            'last_name' => 'new lastname',
            'password' => 'newPassword',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/profile", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'status_code']);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->where('data.name', $data['name'])
            ->where('data.last_name', $data['last_name'])
            ->where('data.email', $user->email)
            ->missing('password')
            ->etc()
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'newname',
            'email' => $user->email,
            'last_name' => 'new lastname',
        ]);

        $this->assertFalse(Hash::check($data['password'], $user->password));
    }

    #[Test]
    public function name_must_be_required() {
        $data = [
            'last_name' => 'Example example'
        ];

        $user = User::find(1);

        $this->actingAsUser($user);
        $response = $this->putJson("{$this->apiBaseUrl}/users/update/profile", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['name']]);
    }

    #[Test]
    public function last_name_must_be_required() {

        $data = [
            'name' => 'David'
        ];
        $user = User::find(1);

        $this->actingAsUser($user);
        $response = $this->putJson("{$this->apiBaseUrl}/users/update/profile", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['last_name']]);

    }
}
