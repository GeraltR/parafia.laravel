<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\InfoItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InfoItemControllerTest extends TestCase
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
        $author = User::factory()->create();

        return [
            'validFrom' => now()->subDays(1)->toDateString(),
            'validTo' => now()->addDays(30)->toDateString(),
            'title' => 'Nowa informacja',
            'shortInfo' => 'Skrócona informacja',
            'description' => '<p>Opis</p>',
            'image' => '/img/test.jpg',
            'progressValue' => 50,
            'progressDescription' => 'Postęp prac',
            'information' => '<p>Informacja</p>',
            'authorId' => $author->id,
        ];
    }

    private function create(array $overrides = []): InfoItem
    {
        return InfoItem::create(array_merge([
            'valid_from' => now()->subDays(1)->toDateString(),
            'valid_to' => now()->addDays(30)->toDateString(),
            'title' => 'Informacja',
            'short_info' => 'x',
            'description' => 'x',
            'image' => '/img/test.jpg',
            'progress_value' => 10,
            'progress_description' => 'x',
        ], $overrides));
    }

    public function test_index_returns_only_currently_active_items(): void
    {
        $this->create(['title' => 'Aktywna']);
        $this->create([
            'title' => 'Zakończona',
            'valid_from' => now()->subDays(10)->toDateString(),
            'valid_to' => now()->subDays(1)->toDateString(),
        ]);
        $this->create([
            'title' => 'Przyszła',
            'valid_from' => now()->addDays(1)->toDateString(),
            'valid_to' => now()->addDays(10)->toDateString(),
        ]);

        $response = $this->getJson('/api/informacje');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Aktywna');
    }

    public function test_manage_requires_authentication(): void
    {
        $response = $this->getJson('/api/informacje/manage');

        $response->assertStatus(401);
    }

    public function test_manage_returns_all_items_including_expired_and_future(): void
    {
        $this->create(['title' => 'Aktywna']);
        $this->create([
            'title' => 'Zakończona',
            'valid_from' => now()->subDays(10)->toDateString(),
            'valid_to' => now()->subDays(1)->toDateString(),
        ]);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/informacje/manage');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->postJson('/api/informacje', $this->payload());

        $response->assertStatus(401);
    }

    public function test_store_forbidden_for_viewer(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/informacje', $this->payload());

        $response->assertStatus(403);
    }

    public function test_store_allowed_for_editor(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/informacje', $this->payload());

        $response->assertCreated();
        $response->assertJsonPath('data.title', 'Nowa informacja');
        $response->assertJsonPath('data.progressValue', 50);
        $this->assertNotNull($response->json('data.author'));
    }

    public function test_store_rejects_valid_to_before_valid_from(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $payload = $this->payload();
        $payload['validFrom'] = now()->addDays(10)->toDateString();
        $payload['validTo'] = now()->addDays(5)->toDateString();

        $response = $this->postJson('/api/informacje', $payload);

        $response->assertStatus(422);
    }

    public function test_update_persists_changes(): void
    {
        $item = $this->create();
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson("/api/informacje/{$item->id}", $this->payload());

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Nowa informacja');
    }

    public function test_destroy_forbidden_for_viewer(): void
    {
        $item = $this->create();
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->deleteJson("/api/informacje/{$item->id}");

        $response->assertStatus(403);
    }

    public function test_destroy_allowed_for_editor(): void
    {
        $item = $this->create();
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->deleteJson("/api/informacje/{$item->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('info_items', ['id' => $item->id]);
    }

    public function test_upload_image_requires_write_permission(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/informacje/upload-image', [
            'image' => UploadedFile::fake()->image('info.png'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_image_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/informacje/upload-image', [
            'image' => UploadedFile::fake()->image('info.png'),
        ]);

        $response->assertOk();
        $this->assertStringContainsString('/storage/info-items/', $response->json('url'));
    }
}
