<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_PASSWORD = 'Str0ng!Passw0rd';

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_index_returns_users_sorted_by_permission_then_name(): void
    {
        $viewer = User::factory()->create(['name' => 'Zenon', 'permission_level' => PermissionLevel::Viewer]);
        $editorB = User::factory()->create(['name' => 'Beata', 'permission_level' => PermissionLevel::Editor]);
        $editorA = User::factory()->create(['name' => 'Alicja', 'permission_level' => PermissionLevel::Editor]);
        $supervisor = User::factory()->create(['name' => 'Marek', 'permission_level' => PermissionLevel::Supervisor]);

        $this->actingAs($supervisor, 'sanctum');

        $response = $this->getJson('/api/users');

        $response->assertOk();
        $response->assertJsonPath('data.0.name', $supervisor->name);
        $response->assertJsonPath('data.1.name', $editorA->name);
        $response->assertJsonPath('data.2.name', $editorB->name);
        $response->assertJsonPath('data.3.name', $viewer->name);
    }

    public function test_index_hides_other_users_email_from_viewer(): void
    {
        $viewer = User::factory()->create(['permission_level' => PermissionLevel::Viewer, 'email' => 'ja@example.com']);
        User::factory()->create(['email' => 'ukryty@example.com']);
        $this->actingAs($viewer, 'sanctum');

        $response = $this->getJson('/api/users');

        $response->assertOk();
        $byEmail = collect($response->json('data'))->keyBy('id');
        $this->assertNull($byEmail[User::where('email', 'ukryty@example.com')->first()->id]['email']);
        $this->assertEquals('ja@example.com', $byEmail[$viewer->id]['email']);
    }

    public function test_index_shows_email_for_editor_and_above(): void
    {
        $editor = User::factory()->create(['permission_level' => PermissionLevel::Editor]);
        User::factory()->create(['email' => 'widoczny@example.com']);
        $this->actingAs($editor, 'sanctum');

        $response = $this->getJson('/api/users');

        $response->assertOk();
        $emails = collect($response->json('data'))->pluck('email');
        $this->assertTrue($emails->contains('widoczny@example.com'));
    }

    public function test_user_can_change_own_password(): void
    {
        $user = User::factory()->create(['permission_level' => PermissionLevel::Viewer]);
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson("/api/users/{$user->id}/password", [
            'newPassword' => self::VALID_PASSWORD,
            'newPasswordConfirmation' => self::VALID_PASSWORD,
        ]);

        $response->assertStatus(204);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check(self::VALID_PASSWORD, $user->fresh()->password));
    }

    public function test_non_supervisor_cannot_change_other_users_password(): void
    {
        $admin = User::factory()->create(['permission_level' => PermissionLevel::Administrator]);
        $other = User::factory()->create(['permission_level' => PermissionLevel::Viewer]);
        $this->actingAs($admin, 'sanctum');

        $response = $this->putJson("/api/users/{$other->id}/password", [
            'newPassword' => self::VALID_PASSWORD,
            'newPasswordConfirmation' => self::VALID_PASSWORD,
        ]);

        $response->assertStatus(403);
    }

    public function test_supervisor_can_change_other_users_password(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor]);
        $other = User::factory()->create(['permission_level' => PermissionLevel::Viewer]);
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->putJson("/api/users/{$other->id}/password", [
            'newPassword' => self::VALID_PASSWORD,
            'newPasswordConfirmation' => self::VALID_PASSWORD,
        ]);

        $response->assertStatus(204);
    }

    public function test_password_change_requires_matching_confirmation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson("/api/users/{$user->id}/password", [
            'newPassword' => self::VALID_PASSWORD,
            'newPasswordConfirmation' => 'Different1!Password',
        ]);

        $response->assertStatus(422);
    }

    public function test_password_change_rejects_weak_password(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->putJson("/api/users/{$user->id}/password", [
            'newPassword' => 'short',
            'newPasswordConfirmation' => 'short',
        ]);

        $response->assertStatus(422);
    }

    public function test_only_supervisor_can_update_user(): void
    {
        $admin = User::factory()->create(['permission_level' => PermissionLevel::Administrator]);
        $other = User::factory()->create(['name' => 'Stary', 'email' => 'stary@example.com']);
        $this->actingAs($admin, 'sanctum');

        $response = $this->putJson("/api/users/{$other->id}", [
            'name' => 'Nowy',
            'email' => 'nowy@example.com',
            'permissionLevel' => PermissionLevel::Editor->value,
        ]);

        $response->assertStatus(403);
    }

    public function test_supervisor_can_update_user(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor]);
        $other = User::factory()->create(['name' => 'Stary', 'email' => 'stary@example.com', 'permission_level' => PermissionLevel::Viewer]);
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->putJson("/api/users/{$other->id}", [
            'name' => 'Nowy',
            'email' => 'nowy@example.com',
            'permissionLevel' => PermissionLevel::Editor->value,
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Nowy');
        $response->assertJsonPath('data.email', 'nowy@example.com');
        $response->assertJsonPath('data.permissionLevel', PermissionLevel::Editor->value);
        $this->assertDatabaseHas('users', ['id' => $other->id, 'name' => 'Nowy', 'email' => 'nowy@example.com']);
    }

    public function test_update_user_rejects_duplicate_email(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor]);
        $other = User::factory()->create();
        $taken = User::factory()->create(['email' => 'zajety@example.com']);
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->putJson("/api/users/{$other->id}", [
            'name' => 'Nowy',
            'email' => 'zajety@example.com',
            'permissionLevel' => PermissionLevel::Editor->value,
        ]);

        $response->assertStatus(422);
    }

    public function test_update_user_allows_keeping_own_email(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor, 'email' => 'super@example.com']);
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->putJson("/api/users/{$supervisor->id}", [
            'name' => 'Zaktualizowany',
            'email' => 'super@example.com',
            'permissionLevel' => PermissionLevel::Supervisor->value,
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Zaktualizowany');
    }

    public function test_supervisor_cannot_demote_own_account(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor, 'email' => 'super@example.com']);
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->putJson("/api/users/{$supervisor->id}", [
            'name' => 'Super',
            'email' => 'super@example.com',
            'permissionLevel' => PermissionLevel::Administrator->value,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('users', ['id' => $supervisor->id, 'permission_level' => PermissionLevel::Supervisor]);
    }

    public function test_only_supervisor_can_create_user(): void
    {
        $admin = User::factory()->create(['permission_level' => PermissionLevel::Administrator]);
        $this->actingAs($admin, 'sanctum');

        $response = $this->postJson('/api/users', [
            'name' => 'Nowy Użytkownik',
            'email' => 'nowy@example.com',
            'password' => self::VALID_PASSWORD,
            'passwordConfirmation' => self::VALID_PASSWORD,
            'permissionLevel' => PermissionLevel::Viewer->value,
        ]);

        $response->assertStatus(403);
    }

    public function test_supervisor_can_create_user(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor]);
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->postJson('/api/users', [
            'name' => 'Nowy Użytkownik',
            'email' => 'nowy@example.com',
            'password' => self::VALID_PASSWORD,
            'passwordConfirmation' => self::VALID_PASSWORD,
            'permissionLevel' => PermissionLevel::Editor->value,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.email', 'nowy@example.com');
        $response->assertJsonPath('data.permissionLevel', PermissionLevel::Editor->value);
        $this->assertDatabaseHas('users', ['email' => 'nowy@example.com']);
    }

    public function test_create_user_requires_unique_email(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor]);
        $existing = User::factory()->create();
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->postJson('/api/users', [
            'name' => 'Nowy Użytkownik',
            'email' => $existing->email,
            'password' => self::VALID_PASSWORD,
            'passwordConfirmation' => self::VALID_PASSWORD,
            'permissionLevel' => PermissionLevel::Viewer->value,
        ]);

        $response->assertStatus(422);
    }

    public function test_only_supervisor_can_delete_user(): void
    {
        $admin = User::factory()->create(['permission_level' => PermissionLevel::Administrator]);
        $other = User::factory()->create();
        $this->actingAs($admin, 'sanctum');

        $response = $this->deleteJson("/api/users/{$other->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $other->id]);
    }

    public function test_supervisor_can_delete_other_user(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor]);
        $other = User::factory()->create();
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->deleteJson("/api/users/{$other->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $other->id]);
    }

    public function test_supervisor_cannot_delete_own_account(): void
    {
        $supervisor = User::factory()->create(['permission_level' => PermissionLevel::Supervisor]);
        $this->actingAs($supervisor, 'sanctum');

        $response = $this->deleteJson("/api/users/{$supervisor->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('users', ['id' => $supervisor->id]);
    }
}
