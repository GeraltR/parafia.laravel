<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\AssociationsConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssociationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_show_is_public(): void
    {
        $config = AssociationsConfig::create([]);
        $config->associations()->create(['name' => 'Ministranci', 'link' => '#', 'order' => 0]);

        $response = $this->getJson('/api/associations');

        $response->assertOk();
        $response->assertJsonCount(1, 'data.items');
    }

    public function test_show_creates_default_row_when_missing(): void
    {
        $this->assertDatabaseCount('associations_configs', 0);

        $response = $this->getJson('/api/associations');

        $response->assertOk();
        $response->assertJsonCount(0, 'data.items');
        $this->assertDatabaseCount('associations_configs', 1);
    }

    public function test_update_requires_authentication(): void
    {
        AssociationsConfig::create([]);

        $response = $this->putJson('/api/associations', ['config' => [], 'items' => []]);

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_viewer(): void
    {
        AssociationsConfig::create([]);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->putJson('/api/associations', ['config' => [], 'items' => []]);

        $response->assertStatus(403);
    }

    public function test_update_creates_and_orders_items(): void
    {
        AssociationsConfig::create([]);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/associations', [
            'config' => ['nameSize' => '0.9rem'],
            'items' => [
                ['name' => 'Ministranci', 'link' => 'https://example.com/ministranci'],
                ['name' => 'Caritas', 'link' => 'https://example.com/caritas'],
            ],
        ]);

        $response->assertOk();
        $response->assertJsonCount(2, 'data.items');
        $response->assertJsonPath('data.items.0.name', 'Ministranci');
        $response->assertJsonPath('data.items.1.order', 1);
        $response->assertJsonPath('data.config.nameSize', '0.9rem');
    }

    public function test_update_reorders_via_move(): void
    {
        $config = AssociationsConfig::create([]);
        $first = $config->associations()->create(['name' => 'A', 'link' => '#', 'order' => 0]);
        $second = $config->associations()->create(['name' => 'B', 'link' => '#', 'order' => 1]);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/associations', [
            'config' => [],
            'items' => [
                ['id' => $second->id, 'name' => 'B', 'link' => '#'],
                ['id' => $first->id, 'name' => 'A', 'link' => '#'],
            ],
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.items.0.name', 'B');
        $this->assertDatabaseHas('associations', ['id' => $second->id, 'order' => 0]);
        $this->assertDatabaseHas('associations', ['id' => $first->id, 'order' => 1]);
    }

    public function test_update_removes_items_missing_from_payload(): void
    {
        $config = AssociationsConfig::create([]);
        $config->associations()->create(['name' => 'Do usunięcia', 'link' => '#', 'order' => 0]);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/associations', ['config' => [], 'items' => []]);

        $response->assertOk();
        $this->assertDatabaseCount('associations', 0);
    }

    public function test_upload_image_requires_write_permission(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/associations/upload-image', [
            'image' => UploadedFile::fake()->image('logo.png'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_image_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/associations/upload-image', [
            'image' => UploadedFile::fake()->image('logo.png'),
        ]);

        $response->assertOk();
        $this->assertStringContainsString('/storage/associations/', $response->json('url'));
    }
}
