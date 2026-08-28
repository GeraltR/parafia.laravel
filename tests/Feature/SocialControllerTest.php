<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\FooterConfig;
use App\Models\Social;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function payload(): array
    {
        return [
            'facebook' => 'https://facebook.com/parafia',
            'youtube' => 'https://youtube.com/parafia',
            'x' => '',
            'instagram' => '',
            'tiktok' => '',
            'pinterest' => '',
            'linkedin' => '',
        ];
    }

    public function test_index_is_public(): void
    {
        $footer = FooterConfig::create([]);
        Social::create([
            'footer_config_id' => $footer->id,
            'social_name' => 'facebook',
            'social_link' => 'https://facebook.com/parafia',
            'visibility' => true,
        ]);

        $response = $this->getJson('/api/social');

        $response->assertOk();
        $response->assertJsonPath('data.facebook', 'https://facebook.com/parafia');
        $response->assertJsonPath('data.youtube', '');
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/social', $this->payload());

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_editor(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/social', $this->payload());

        $response->assertStatus(403);
    }

    public function test_update_allowed_for_administrator(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->putJson('/api/social', $this->payload());

        $response->assertOk();
        $response->assertJsonPath('data.facebook', 'https://facebook.com/parafia');
        $response->assertJsonPath('data.youtube', 'https://youtube.com/parafia');

        $this->assertDatabaseHas('social', [
            'social_name' => 'facebook',
            'social_link' => 'https://facebook.com/parafia',
        ]);
    }

    public function test_update_preserves_existing_visibility(): void
    {
        $footer = FooterConfig::create([]);
        Social::create([
            'footer_config_id' => $footer->id,
            'social_name' => 'facebook',
            'social_link' => '',
            'visibility' => true,
        ]);
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->putJson('/api/social', $this->payload());

        $response->assertOk();
        $this->assertDatabaseHas('social', [
            'social_name' => 'facebook',
            'social_link' => 'https://facebook.com/parafia',
            'visibility' => true,
        ]);
    }
}
