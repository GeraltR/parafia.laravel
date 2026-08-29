<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\FooterConfig;
use App\Models\OfficeHour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterControllerTest extends TestCase
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
            'officeTitle' => 'Godziny kancelarii',
            'officeNote' => 'W pilnych sprawach dzwoń.',
            'mapEmbedUrl' => 'https://maps.example/embed',
            'mapLink' => 'https://maps.example/link',
            'config' => [
                'bgColor' => '#111111',
                'titleFont' => 'Inter',
                'titleSize' => '0.8rem',
                'titleColor' => '#eeeeee',
            ],
            'officeHours' => [
                ['day' => 'Poniedziałek', 'hoursOn' => '16:00', 'hoursEnd' => '17:30'],
                ['day' => 'Środa', 'hoursOn' => '16:00', 'hoursEnd' => '17:30'],
            ],
        ];
    }

    public function test_show_is_public(): void
    {
        FooterConfig::create(['copyright_text' => 'Test copyright']);

        $response = $this->getJson('/api/footer');

        $response->assertOk();
        $response->assertJsonPath('data.copyrightText', 'Test copyright');
    }

    public function test_show_creates_default_row_when_missing(): void
    {
        $this->assertDatabaseCount('footer_configs', 0);

        $response = $this->getJson('/api/footer');

        $response->assertOk();
        $response->assertJsonCount(0, 'data.officeHours');
        $response->assertJsonCount(0, 'data.legalLinks');
        $response->assertJsonPath('data.officeTitle', 'Godziny otwarcia kancelarii');
        $this->assertDatabaseCount('footer_configs', 1);
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/footer', $this->payload());

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_viewer(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->putJson('/api/footer', $this->payload());

        $response->assertStatus(403);
    }

    public function test_update_forbidden_for_editor(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/footer', $this->payload());

        $response->assertStatus(403);
    }

    public function test_update_allowed_for_administrator_persists_fields_and_office_hours(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->putJson('/api/footer', $this->payload());

        $response->assertOk();
        $response->assertJsonPath('data.officeTitle', 'Godziny kancelarii');
        $response->assertJsonPath('data.officeNote', 'W pilnych sprawach dzwoń.');
        $response->assertJsonPath('data.mapEmbedUrl', 'https://maps.example/embed');
        $response->assertJsonPath('data.config.bgColor', '#111111');
        $response->assertJsonPath('data.config.titleFont', 'Inter');
        $response->assertJsonCount(2, 'data.officeHours');
        $response->assertJsonPath('data.officeHours.0.day', 'Poniedziałek');
        $response->assertJsonPath('data.officeHours.0.hoursOn', '16:00');
        $response->assertJsonPath('data.officeHours.0.hoursEnd', '17:30');

        $this->assertDatabaseHas('footer_configs', ['office_title' => 'Godziny kancelarii']);
        $this->assertDatabaseCount('office_hours', 2);
    }

    public function test_update_removes_office_hours_not_included_in_payload(): void
    {
        $footer = FooterConfig::create();
        $keep = $footer->officeHours()->create(['day' => 'Piątek', 'hours_on' => '10:00', 'hours_end' => '11:00']);
        $remove = $footer->officeHours()->create(['day' => 'Sobota', 'hours_on' => '10:00', 'hours_end' => '11:00']);
        $this->actingAsLevel(PermissionLevel::Administrator);

        $payload = $this->payload();
        $payload['officeHours'] = [
            ['id' => $keep->id, 'day' => 'Piątek', 'hoursOn' => '10:00', 'hoursEnd' => '12:00'],
        ];

        $response = $this->putJson('/api/footer', $payload);

        $response->assertOk();
        $this->assertDatabaseHas('office_hours', ['id' => $keep->id, 'hours_end' => '12:00']);
        $this->assertDatabaseMissing('office_hours', ['id' => $remove->id]);
    }

    public function test_update_rejects_invalid_hours_format(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);

        $payload = $this->payload();
        $payload['officeHours'][0]['hoursOn'] = 'not-a-time';

        $response = $this->putJson('/api/footer', $payload);

        $response->assertStatus(422);
    }
}
