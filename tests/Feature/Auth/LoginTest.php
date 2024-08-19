<?php

namespace Tests\Feature\Auth;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * A basic feature test example.
     */
    #[Test]
    public function a_user_can_login(): void {
        $credentials = ['email' => 'john@doe.com', 'password' => 'password'];


        $response = $this->post("{$this->apiBaseUrl}/auth/login", $credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    #[Test]
    public function a_non_existent_user_can_not_login() {
        $credentials = ['email' => 'example@doe.com', 'password' => 'password'];

        $response = $this->post("{$this->apiBaseUrl}/auth/login", $credentials);

        $response->assertStatus(401);
        $response->assertJsonFragment(['status_code' => 401, 'message' => 'Unauthorized']);
    }

    #[Test]
    public function email_is_required() {
        $credentials = ['password' => 'password'];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_valid() {
        $credentials = ['email' => '19MSSJA.DAS', 'password' => 'password'];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['email']]);
    }

    #[Test]
    public function email_must_be_a_string() {
        $credentials = ['email' => 23213123, 'password' => 'password'];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['email']]);
    }

    #[Test]
    public function password_is_required() {
        $credentials = ['email' => 'john@doe.com'];

        $response = $this->post("{$this->apiBaseUrl}/auth/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_be_at_least_8_characters() {
        $credentials = ['email' => 23213123, 'password' => '54SA'];

        $response = $this->post("{$this->apiBaseUrl}/auth/login", $credentials);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
    }
}
