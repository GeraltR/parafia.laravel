<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsItemControllerTest extends TestCase
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
            'date' => now()->toDateString(),
            'title' => 'Nowa aktualność',
            'excerpt' => 'Krótki opis',
            'image' => '/img/news-1.jpg',
            'body' => '<p>Pełna treść</p>',
            'showImageOnFullContent' => true,
            'authorId' => $author->id,
        ];
    }

    public function test_store_persists_show_image_on_full_content_flag(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $payload = $this->payload();
        $payload['showImageOnFullContent'] = false;

        $response = $this->postJson('/api/news', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.showImageOnFullContent', false);
        $this->assertDatabaseHas('news_items', ['title' => 'Nowa aktualność', 'show_image_on_full_content' => false]);
    }

    public function test_store_allows_omitting_image(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $payload = $this->payload();
        $payload['image'] = null;

        $response = $this->postJson('/api/news', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.image', null);
    }

    public function test_index_returns_latest_four_ordered_by_date_desc(): void
    {
        for ($i = 0; $i < 6; $i++) {
            NewsItem::create([
                'date' => now()->subDays($i),
                'title' => "Wpis {$i}",
                'excerpt' => 'x',
                'image' => '/img/x.jpg',
            ]);
        }

        $response = $this->getJson('/api/news');

        $response->assertOk();
        $response->assertJsonCount(4, 'data');
        $response->assertJsonPath('data.0.title', 'Wpis 0');
    }

    public function test_manage_requires_authentication(): void
    {
        $response = $this->getJson('/api/news/manage');

        $response->assertStatus(401);
    }

    public function test_store_forbidden_for_viewer(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/news', $this->payload());

        $response->assertStatus(403);
    }

    public function test_store_allowed_for_editor(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/news', $this->payload());

        $response->assertCreated();
        $response->assertJsonPath('data.title', 'Nowa aktualność');
        $response->assertJsonPath('data.body', '<p>Pełna treść</p>');
        $this->assertNotNull($response->json('data.author'));
    }

    public function test_update_persists_changes(): void
    {
        $news = NewsItem::create(['date' => now(), 'title' => 'Stare', 'excerpt' => 'x', 'image' => '/img/x.jpg']);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson("/api/news/{$news->id}", $this->payload());

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Nowa aktualność');
    }

    public function test_destroy_allowed_for_editor(): void
    {
        $news = NewsItem::create(['date' => now(), 'title' => 'X', 'excerpt' => 'x', 'image' => '/img/x.jpg']);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->deleteJson("/api/news/{$news->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('news_items', ['id' => $news->id]);
    }

    public function test_upload_image_requires_write_permission(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/news/upload-image', [
            'image' => UploadedFile::fake()->image('news.png'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_image_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/news/upload-image', [
            'image' => UploadedFile::fake()->image('news.png'),
        ]);

        $response->assertOk();
        $this->assertStringContainsString('/storage/news/', $response->json('url'));
    }
}
