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

    public function test_manage_orders_by_date_desc_then_time_asc(): void
    {
        MassIntention::create(['date' => '2026-08-26', 'time' => '18:00', 'intention' => 'Stare wieczorem']);
        MassIntention::create(['date' => '2026-08-26', 'time' => '07:00', 'intention' => 'Stare rano']);
        MassIntention::create(['date' => '2026-08-28', 'time' => '18:00', 'intention' => 'Nowe wieczorem']);
        MassIntention::create(['date' => '2026-08-28', 'time' => '07:00', 'intention' => 'Nowe rano']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/mass-intentions/manage');

        $response->assertOk();
        $response->assertJsonPath('data.items.0.intention', 'Nowe rano');
        $response->assertJsonPath('data.items.1.intention', 'Nowe wieczorem');
        $response->assertJsonPath('data.items.2.intention', 'Stare rano');
        $response->assertJsonPath('data.items.3.intention', 'Stare wieczorem');
    }

    public function test_manage_paginates_by_fifty(): void
    {
        for ($i = 0; $i < 60; $i++) {
            MassIntention::create(['date' => now()->addDays($i), 'time' => '07:00', 'intention' => "Intencja {$i}"]);
        }
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/mass-intentions/manage');

        $response->assertOk();
        $response->assertJsonCount(50, 'data.items');
        $response->assertJsonPath('data.meta.currentPage', 1);
        $response->assertJsonPath('data.meta.lastPage', 2);
        $response->assertJsonPath('data.meta.total', 60);

        $secondPage = $this->getJson('/api/mass-intentions/manage?page=2');
        $secondPage->assertOk();
        $secondPage->assertJsonCount(10, 'data.items');
    }

    public function test_manage_search_matches_intention_text(): void
    {
        MassIntention::create(['date' => '2026-08-26', 'time' => '07:00', 'intention' => 'Za Roberta Szczerbinę']);
        MassIntention::create(['date' => '2026-08-27', 'time' => '07:00', 'intention' => 'Za parafian']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/mass-intentions/manage?search=Szczerbin');

        $response->assertOk();
        $response->assertJsonCount(1, 'data.items');
        $response->assertJsonPath('data.items.0.intention', 'Za Roberta Szczerbinę');
    }

    public function test_manage_search_matches_day_description(): void
    {
        MassIntention::create(['date' => '2026-08-29', 'time' => '07:00', 'intention' => 'X', 'day_description' => 'Wspomnienie św. Jana Chrzciciela']);
        MassIntention::create(['date' => '2026-08-30', 'time' => '07:00', 'intention' => 'Y']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/mass-intentions/manage?search=Chrzciciela');

        $response->assertOk();
        $response->assertJsonCount(1, 'data.items');
        $response->assertJsonPath('data.items.0.intention', 'X');
    }

    public function test_manage_search_matches_date_ignoring_leading_zeros(): void
    {
        MassIntention::create(['date' => '2026-09-05', 'time' => '07:00', 'intention' => 'Piąty wrzesień']);
        MassIntention::create(['date' => '2026-09-15', 'time' => '07:00', 'intention' => 'Piętnasty wrzesień']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/mass-intentions/manage?search=' . urlencode('5.9'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data.items');
        $response->assertJsonPath('data.items.0.intention', 'Piąty wrzesień');
    }

    public function test_manage_search_matches_padded_date(): void
    {
        MassIntention::create(['date' => '2026-09-05', 'time' => '07:00', 'intention' => 'Piąty wrzesień']);
        MassIntention::create(['date' => '2026-09-15', 'time' => '07:00', 'intention' => 'Piętnasty wrzesień']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/mass-intentions/manage?search=' . urlencode('05.09'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data.items');
        $response->assertJsonPath('data.items.0.intention', 'Piąty wrzesień');
    }

    public function test_print_list_requires_authentication(): void
    {
        $response = $this->getJson('/api/mass-intentions/print');

        $response->assertStatus(401);
    }

    public function test_print_list_returns_from_date_onward_ordered_ascending(): void
    {
        MassIntention::create(['date' => '2026-09-04', 'time' => '07:00', 'intention' => 'Przed zakresem']);
        MassIntention::create(['date' => '2026-09-06', 'time' => '18:00', 'intention' => 'Szósty wieczorem']);
        MassIntention::create(['date' => '2026-09-06', 'time' => '07:00', 'intention' => 'Szósty rano']);
        MassIntention::create(['date' => '2026-09-20', 'time' => '07:00', 'intention' => 'Najmłodszy']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/mass-intentions/print?from=2026-09-06');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonPath('data.0.intention', 'Szósty rano');
        $response->assertJsonPath('data.1.intention', 'Szósty wieczorem');
        $response->assertJsonPath('data.2.intention', 'Najmłodszy');
    }

    public function test_print_list_without_from_returns_everything(): void
    {
        MassIntention::create(['date' => '2026-09-04', 'time' => '07:00', 'intention' => 'A']);
        MassIntention::create(['date' => '2026-09-06', 'time' => '07:00', 'intention' => 'B']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/mass-intentions/print');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
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
