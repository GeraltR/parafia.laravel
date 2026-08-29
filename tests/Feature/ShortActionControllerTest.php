<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\ShortActionItem;
use App\Models\ShortActionsConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShortActionControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function seedSixItems(): array
    {
        ShortActionsConfig::create([]);

        return collect(range(1, 6))
            ->map(fn ($i) => ShortActionItem::create([
                'icon' => 'mass',
                'title' => "Tytuł {$i}",
                'description' => "Opis {$i}",
                'href' => '#kontakt',
            ]))
            ->all();
    }

    private function payloadFor(array $items): array
    {
        return [
            'config' => [
                'titleSize' => '1rem',
                'titleColor' => '#000000',
                'subtitleSize' => '0.8rem',
                'subtitleColor' => '#333333',
                'bgColor' => '#ffffff',
                'bgColorHover' => '#eeeeee',
            ],
            'items' => collect($items)->map(fn ($item, $i) => [
                'id' => $item->id,
                'icon' => 'mass',
                'title' => "Nowy tytuł {$i}",
                'description' => "Nowy opis {$i}",
                'href' => '#kontakt',
            ])->values()->all(),
        ];
    }

    public function test_show_is_public(): void
    {
        $this->seedSixItems();

        $response = $this->getJson('/api/short-actions');

        $response->assertOk();
        $response->assertJsonCount(6, 'data.items');
    }

    public function test_show_creates_default_config_and_backfills_to_six_items(): void
    {
        $this->assertDatabaseCount('short_actions_configs', 0);
        $this->assertDatabaseCount('short_action_items', 0);

        $response = $this->getJson('/api/short-actions');

        $response->assertOk();
        $response->assertJsonCount(6, 'data.items');
        $this->assertDatabaseCount('short_actions_configs', 1);
        $this->assertDatabaseCount('short_action_items', 6);
    }

    public function test_show_backfills_only_missing_items_when_some_already_exist(): void
    {
        ShortActionsConfig::create([]);
        ShortActionItem::create(['icon' => 'mass', 'title' => 'A', 'description' => 'B', 'href' => '/']);

        $response = $this->getJson('/api/short-actions');

        $response->assertOk();
        $response->assertJsonCount(6, 'data.items');
        $this->assertDatabaseCount('short_action_items', 6);
    }

    public function test_update_requires_authentication(): void
    {
        $items = $this->seedSixItems();

        $response = $this->putJson('/api/short-actions', $this->payloadFor($items));

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_viewer(): void
    {
        $items = $this->seedSixItems();
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->putJson('/api/short-actions', $this->payloadFor($items));

        $response->assertStatus(403);
    }

    public function test_update_forbidden_for_editor(): void
    {
        $items = $this->seedSixItems();
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/short-actions', $this->payloadFor($items));

        $response->assertStatus(403);
    }

    public function test_update_allowed_for_administrator(): void
    {
        $items = $this->seedSixItems();
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->putJson('/api/short-actions', $this->payloadFor($items));

        $response->assertOk();
        $response->assertJsonPath('data.items.0.title', 'Nowy tytuł 0');
        $response->assertJsonPath('data.config.titleColor', '#000000');
    }

    public function test_update_requires_exactly_six_items(): void
    {
        $items = $this->seedSixItems();
        $this->actingAsLevel(PermissionLevel::Administrator);

        $payload = $this->payloadFor($items);
        array_pop($payload['items']);

        $response = $this->putJson('/api/short-actions', $payload);

        $response->assertStatus(422);
    }

    public function test_update_rejects_unknown_item_id(): void
    {
        $items = $this->seedSixItems();
        $this->actingAsLevel(PermissionLevel::Administrator);

        $payload = $this->payloadFor($items);
        $payload['items'][0]['id'] = 99999;

        $response = $this->putJson('/api/short-actions', $payload);

        $response->assertStatus(422);
    }

    public function test_upload_icon_requires_write_permission(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/short-actions/upload-icon', [
            'icon' => UploadedFile::fake()->image('icon.png'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_icon_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->postJson('/api/short-actions/upload-icon', [
            'icon' => UploadedFile::fake()->image('icon.png'),
        ]);

        $response->assertOk();
        $url = $response->json('url');
        $this->assertStringContainsString('/storage/short-actions/', $url);
    }
}
