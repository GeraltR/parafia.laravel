<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\ContactAddress;
use App\Models\FooterConfig;
use App\Models\Social;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactAddressControllerTest extends TestCase
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
            'street' => 'ul. Nowa 5',
            'city' => 'Nowowo',
            'postCode' => '11-111',
            'phone' => '+48 999 888 777',
            'social' => [
                'facebook' => true,
                'youtube' => false,
                'x' => false,
                'instagram' => true,
                'tiktok' => false,
                'pinterest' => false,
                'linkedin' => false,
            ],
        ];
    }

    public function test_show_is_public(): void
    {
        $footer = FooterConfig::create([]);
        ContactAddress::create([
            'footer_config_id' => $footer->id,
            'street' => 'ul. Testowa 1',
            'city' => 'Testowo',
            'post_code' => '00-000',
            'phone' => '+48 111 222 333',
        ]);

        $response = $this->getJson('/api/contact-addresses');

        $response->assertOk();
        $response->assertJsonPath('data.address', 'ul. Testowa 1, 00-000 Testowo');
        $response->assertJsonPath('data.street', 'ul. Testowa 1');
        $response->assertJsonPath('data.city', 'Testowo');
        $response->assertJsonPath('data.postCode', '00-000');
        $response->assertJsonPath('data.phone', '+48 111 222 333');
    }

    public function test_show_creates_default_rows_when_missing(): void
    {
        $this->assertDatabaseCount('footer_configs', 0);
        $this->assertDatabaseCount('contact_addresses', 0);

        $response = $this->getJson('/api/contact-addresses');

        $response->assertOk();
        $this->assertDatabaseCount('footer_configs', 1);
        $this->assertDatabaseCount('contact_addresses', 1);
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/contact-addresses', $this->payload());

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_editor(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/contact-addresses', $this->payload());

        $response->assertStatus(403);
    }

    public function test_update_allowed_for_administrator(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->putJson('/api/contact-addresses', $this->payload());

        $response->assertOk();
        $response->assertJsonPath('data.street', 'ul. Nowa 5');
        $response->assertJsonPath('data.city', 'Nowowo');
        $response->assertJsonPath('data.postCode', '11-111');
        $response->assertJsonPath('data.phone', '+48 999 888 777');
        $response->assertJsonPath('data.social.facebook', true);
        $response->assertJsonPath('data.social.instagram', true);
        $response->assertJsonPath('data.social.youtube', false);

        $this->assertDatabaseHas('contact_addresses', [
            'street' => 'ul. Nowa 5',
            'city' => 'Nowowo',
            'post_code' => '11-111',
            'phone' => '+48 999 888 777',
        ]);
        $this->assertDatabaseHas('social', ['social_name' => 'facebook', 'visibility' => true]);
        $this->assertDatabaseHas('social', ['social_name' => 'youtube', 'visibility' => false]);
    }

    public function test_update_preserves_existing_social_links(): void
    {
        $footer = FooterConfig::create([]);
        Social::create([
            'footer_config_id' => $footer->id,
            'social_name' => 'facebook',
            'social_link' => 'https://facebook.com/parafia',
            'visibility' => false,
        ]);
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->putJson('/api/contact-addresses', $this->payload());

        $response->assertOk();
        $this->assertDatabaseHas('social', [
            'social_name' => 'facebook',
            'social_link' => 'https://facebook.com/parafia',
            'visibility' => true,
        ]);
    }

    public function test_update_rejects_invalid_post_code(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);

        $payload = $this->payload();
        $payload['postCode'] = '11111';

        $response = $this->putJson('/api/contact-addresses', $payload);

        $response->assertStatus(422);
    }
}
