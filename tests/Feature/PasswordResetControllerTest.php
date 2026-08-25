<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    private const NEW_PASSWORD = 'Str0ng!Passw0rd';

    public function test_forgot_password_sends_notification_for_known_email(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'known@example.com']);

        $response = $this->postJson('/api/forgot-password', ['email' => 'known@example.com']);

        $response->assertOk();
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_forgot_password_returns_generic_response_for_unknown_email(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/forgot-password', ['email' => 'nobody@example.com']);

        $response->assertOk();
        Notification::assertNothingSent();
    }

    public function test_forgot_password_requires_valid_email(): void
    {
        $response = $this->postJson('/api/forgot-password', ['email' => 'not-an-email']);

        $response->assertStatus(422);
    }

    public function test_reset_password_updates_password_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'known@example.com']);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'known@example.com',
            'password' => self::NEW_PASSWORD,
            'passwordConfirmation' => self::NEW_PASSWORD,
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->fresh()->password));
    }

    public function test_reset_password_rejects_invalid_token(): void
    {
        $user = User::factory()->create(['email' => 'known@example.com']);

        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'known@example.com',
            'password' => self::NEW_PASSWORD,
            'passwordConfirmation' => self::NEW_PASSWORD,
        ]);

        $response->assertStatus(422);
        $this->assertFalse(Hash::check(self::NEW_PASSWORD, $user->fresh()->password));
    }

    public function test_reset_password_requires_matching_confirmation(): void
    {
        $user = User::factory()->create(['email' => 'known@example.com']);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'known@example.com',
            'password' => self::NEW_PASSWORD,
            'passwordConfirmation' => 'Different1!Password',
        ]);

        $response->assertStatus(422);
    }

    public function test_reset_password_rejects_weak_password(): void
    {
        $user = User::factory()->create(['email' => 'known@example.com']);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'known@example.com',
            'password' => 'short',
            'passwordConfirmation' => 'short',
        ]);

        $response->assertStatus(422);
    }

    public function test_reset_password_consumes_token(): void
    {
        $user = User::factory()->create(['email' => 'known@example.com']);
        $token = Password::createToken($user);

        $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => 'known@example.com',
            'password' => self::NEW_PASSWORD,
            'passwordConfirmation' => self::NEW_PASSWORD,
        ])->assertOk();

        $this->assertDatabaseCount('password_reset_tokens', 0);
    }
}
