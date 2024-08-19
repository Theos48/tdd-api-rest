<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserPasswordTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    #[Test]
    public function an_authenticated_user_can_update_their_password() {
        $data = [
            'old_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/password", $data);
        $user->refresh();

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'status_code']);

        $this->assertTrue(Hash::check($data['password'], $user->password));
        $this->assertNotEquals($data['old_password'], $user->password);
    }

    #[Test]
    public function old_password_is_required() {
        $data = [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['old_password']]);
        $response->assertJsonMissingValidationErrors(['password', 'password_confirmation']);
    }

    #[Test]
    public function old_password_must_be_confirmed() {
        $data = [
            'old_password' => 'wrongpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['old_password']]);
        $response->assertJsonMissingValidationErrors(['password', 'password_confirmation']);
    }

    #[Test]
    public function password_is_required() {
        $data = [
            'old_password' => 'password',
            'password_confirmation' => 'newpassword',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
        $response->assertJsonMissingValidationErrors(['old_password']);
    }

    #[Test]
    public function password_must_have_at_lease_8_characters() {
        $data = [
            'old_password' => 'password',
            'password' => 'new',
            'password_confirmation' => 'new',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
        $response->assertJsonMissingValidationErrors(['old_password']);
    }

    #[Test]
    public function password_must_be_confirmed() {
        $data = [
            'old_password' => 'password',
            'password' => 'password',
            'password_confirmation' => 'passwdasdss'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
        $response->assertJsonMissingValidationErrors(['old_password',]);
    }

    #[Test]
    public function password_confirmation_is_required() {
        $data = [
            'old_password' => 'password',
            'password' => 'newpassword',
        ];

        $user = User::find(1);
        $this->actingAsUser($user);

        $response = $this->putJson("{$this->apiBaseUrl}/users/update/password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password_confirmation']]);
        $response->assertJsonMissingValidationErrors(['old_password',]);
    }
}
