<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\MassIntention;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MassIntentionControllerTest extends TestCase
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
            'date' => now()->addDays(3)->toDateString(),
            'time' => '07:00',
            'intention' => 'Nowa intencja',
            'isHoliday' => false,
            'dayDescription' => null,
        ];
    }

    public function test_index_is_public_and_includes_config(): void
    {
        MassIntention::create(['date' => now()->addDays(2), 'time' => '07:00', 'intention' => 'W intencji parafian']);

        $response = $this->getJson('/api/mass-intentions');

        $response->assertOk();
        $response->assertJsonPath('data.items.0.intention', 'W intencji parafian');
        $response->assertJsonPath('data.config.holidayDescribedColor', '#7bdcb5');
        $response->assertJsonPath('data.config.holidayPlainColor', '#f78da7');
        $response->assertJsonPath('data.config.weekdayColor', '#8ed1fc');
    }

    public function test_index_excludes_past_and_far_future_intentions(): void
    {
        MassIntention::create(['date' => now()->subDay(), 'time' => '07:00', 'intention' => 'Przeszła']);
        MassIntention::create(['date' => now()->addDays(5), 'time' => '07:00', 'intention' => 'W zakresie']);
        MassIntention::create(['date' => now()->addDays(20), 'time' => '07:00', 'intention' => 'Za daleko']);

        $response = $this->getJson('/api/mass-intentions');

        $response->assertOk();
        $response->assertJsonCount(1, 'data.items');
        $response->assertJsonPath('data.items.0.intention', 'W zakresie');
    }

    public function test_manage_requires_authentication(): void
    {
        $response = $this->getJson('/api/mass-intentions/manage');

        $response->assertStatus(401);
    }

    public function test_store_forbidden_for_viewer(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/mass-intentions', $this->payload());

        $response->assertStatus(403);
    }

    public function test_store_allowed_for_editor(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/mass-intentions', $this->payload());

        $response->assertCreated();
        $response->assertJsonPath('data.intention', 'Nowa intencja');
    }

    public function test_creating_holiday_intention_syncs_flag_across_same_day(): void
    {
        $date = now()->addDays(4)->toDateString();
        $existing = MassIntention::create(['date' => $date, 'time' => '07:00', 'intention' => 'Pierwsza msza']);
        $this->actingAsLevel(PermissionLevel::Editor);

        $payload = $this->payload();
        $payload['date'] = $date;
        $payload['time'] = '18:00';
        $payload['isHoliday'] = true;
        $payload['dayDescription'] = 'Uroczystość Wszystkich Świętych';

        $response = $this->postJson('/api/mass-intentions', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('mass_intentions', [
            'id' => $existing->id,
            'is_holiday' => true,
            'day_description' => 'Uroczystość Wszystkich Świętych',
        ]);
    }

    public function test_destroy_allowed_for_editor(): void
    {
        $intention = MassIntention::create(['date' => now()->addDays(2), 'time' => '07:00', 'intention' => 'X']);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->deleteJson("/api/mass-intentions/{$intention->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('mass_intentions', ['id' => $intention->id]);
    }

    public function test_update_config_requires_write_permission(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->putJson('/api/mass-intentions/config', [
            'holidayDescribedColor' => '#111111',
            'holidayPlainColor' => '#222222',
            'weekdayColor' => '#333333',
        ]);

        $response->assertStatus(403);
    }

    public function test_update_config_persists_colors(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/mass-intentions/config', [
            'holidayDescribedColor' => '#111111',
            'holidayPlainColor' => '#222222',
            'weekdayColor' => '#333333',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.holidayDescribedColor', '#111111');
        $this->assertDatabaseHas('mass_intentions_configs', ['holiday_described_color' => '#111111']);
    }
}
