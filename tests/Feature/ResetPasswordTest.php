<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetPasswordTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    #[Test]
    public function an_existing_user_can_reset_their_password() {

        $user = User::find(1);
        $token = Password::createToken($user);

        $data = [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);
        $user->refresh();

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'status_code']);

        $this->assertTrue(Hash::check($data['password'], $user->password));

    }

    #[Test]
    public function password_is_required() {
        $user = User::find(1);
        $token = Password::createToken($user);

        $data = [
            'email' => $user->email,
            'token' => $token,
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
        $response->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function password_must_have_at_lease_8_characters() {
        $user = User::find(1);
        $token = Password::createToken($user);

        $data = [
            'email' => $user->email,
            'token' => $token,
            'password' => 'new',
            'password_confirmation' => 'new',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_be_confirmed() {
        $user = User::find(1);
        $token = Password::createToken($user);


        $data = [
            'email' => $user->email,
            'token' => $token,
            'password' => 'password',
            'password_confirmation' => 'passwdasdss'
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);
    }

    #[Test]
    public function password_confirmation_is_required() {
        $user = User::find(1);
        $token = Password::createToken($user);

        $data = [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors' => ['password']]);

    }

    #[Test]
    public function email_is_required() {
        $user = User::find(1);
        $token = Password::createToken($user);

        $data = [
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function email_must_be_valid() {
        $user = User::find(1);
        $token = Password::createToken($user);

        $data = [
            'email' => 'invalidemail.com',
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function email_must_be_an_existing_email() {
        $user = User::find(1);
        $token = Password::createToken($user);

        $data = [
            'email' => 'notexisting@example.com',
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function token_must_be_valid() {
        $user = User::find(1);
        $token = Password::createToken($user);

        $data = [
            'email' => $user->email,
            'token' => $token . 'dsadsa',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->postJson("{$this->apiBaseUrl}/auth/reset-password", $data);

        $response->assertStatus(500);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonFragment([
            'message' => 'Invalid token'
        ]);
    }

}
