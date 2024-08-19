<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRegisterTest extends TestCase {
    use RefreshDatabase;

    #[Test]
    public function a_user_can_register(): void {
        $data = [
            'name' => 'David',
            'last_name' => 'Example example',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'name', 'last_name', 'email']]);
        $this->assertDatabaseHas('users', [
            'name' => 'David',
            'last_name' => 'Example example',
            'email' => 'test@test.com',
        ]);
    }

    #[Test]
    public function email_is_required() {
        $data = [
            'name' => 'David',
            'last_name' => 'Example example',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function email_must_be_valid() {
        $data = [
            'name' => 'David',
            'last_name' => 'Example example',
            'email' => 'testest.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function email_must_be_unique() {

        User::factory()->create(['email' => 'test@test.com']);

        $data = [
            'name' => 'David',
            'last_name' => 'Example example',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function password_is_required() {
        $data = [
            'name' => 'David',
            'email' => 'test@test.com',
            'last_name' => 'Example example',
            'password_confirmation' => 'password'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_have_at_lease_8_characters() {
        $data = [
            'name' => 'David',
            'email' => 'test@test.com',
            'last_name' => 'Example example',
            'password' => 'passwor',
            'password_confirmation' => 'passwor'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_be_confirmed() {
        $data = [
            'name' => 'David',
            'email' => 'test@test.com',
            'last_name' => 'Example example',
            'password' => 'password',
            'password_confirmation' => 'passwdasdss'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
    }

    #[Test]
    public function password_confirmation_is_required() {
        $data = [
            'name' => 'David',
            'email' => 'test@test.com',
            'last_name' => 'Example example',
            'password' => 'password',

        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password_confirmation']]);
    }

    #[Test]
    public function name_must_be_required() {
        $data = [
            'name' => 'David',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['last_name']]);
    }

    #[Test]
    public function last_name_must_be_required() {
        $data = [
            'last_name' => 'Example example',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post("{$this->apiBaseUrl}/users/store", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['name']]);
    }
}
