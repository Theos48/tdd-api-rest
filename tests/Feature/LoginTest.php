<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
     use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * A basic feature test example.
     */
    #[Test]
    public function a_user_can_login(): void
    {
//        $this->withoutExceptionHandling();
        $credentials = ['email' => 'john@doe.com', 'password' => 'password'];


        $response = $this->post("{$this->apiBaseUrl}/auth/login", $credentials);
//        $response->dump();

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']], );
    }

    #[Test]
    public function a_non_existent_user_can_not_login() {
        $credentials = ['email' => 'john@doe.com', 'password' => 'password'];

        $response = $this->post('api/v1/auth/login', $credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']], );
    }

    public function email_is_required() {
        $credentials = ['email' => 'john@doe.com', 'password' => 'password'];

        $response = $this->post('api/v1/auth/login', $credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']], );
    }

    public function password_is_required() {
        $credentials = ['email' => 'john@doe.com', 'password' => 'password'];

        $response = $this->post('api/v1/auth/login', $credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']], );
    }
//    public function a_user_can_logout(): void
}
