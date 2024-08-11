<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    #[Test]
    public function forgot_password_sends_email_to_registered_user() {
        Notification::fake();

        $data = ['email' => 'john@doe.com'];
        $response = $this->postJson("{$this->apiBaseUrl}/auth/forgot-password", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'status_code']);

        $user = User::find(1);

        Notification::assertSentTo([$user], function (ResetPasswordNotification $notification, array $channels) use ($user) {
            $parts = parse_url($notification->url);

            $this->assertEquals('https', $parts['scheme']);
            $this->assertEquals('example.com', $parts['host']);
            $this->assertEquals('/reset-password', $parts['path']);

            parse_str($parts['query'], $query);
            $this->assertArrayHasKey('token', $query);
            $this->assertArrayHasKey('email', $query);
            $this->assertNotEmpty($query['token']);
            $this->assertEquals($user->email, $query['email']);

            return true;
        });
    }

    #[Test]
    public function forgot_password_sends_email_to_registered_user_when_not_exists() {
        Notification::fake();
        $data = ['email' => 'john@noexist.com'];
        $response = $this->postJson("{$this->apiBaseUrl}/auth/forgot-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function email_must_be_valid() {
        $data = ['email' => 'johnnovalida.com'];
        $response = $this->postJson("{$this->apiBaseUrl}/auth/forgot-password", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'status_code', 'errors']);
        $response->assertJsonValidationErrors('email');
    }

}
