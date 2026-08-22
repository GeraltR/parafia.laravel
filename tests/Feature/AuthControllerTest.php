<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_succeeds_with_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'permission_level' => PermissionLevel::Administrator,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'permissionLevel', 'canWrite']]);
        $response->assertJsonPath('user.permissionLevel', 3);
        $response->assertJsonPath('user.canWrite', true);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_fails_for_unknown_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nobody@example.com',
            'password' => 'whatever',
        ]);

        $response->assertStatus(422);
    }

    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_me_returns_current_user(): void
    {
        $user = User::factory()->create(['permission_level' => PermissionLevel::Viewer]);
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/me');

        $response->assertOk();
        $response->assertJsonPath('data.email', $user->email);
        $response->assertJsonPath('data.permissionLevel', 0);
        $response->assertJsonPath('data.canWrite', false);
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
