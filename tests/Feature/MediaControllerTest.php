<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/media');

        $response->assertStatus(401);
    }

    public function test_index_returns_all_media_ordered_by_newest_first(): void
    {
        $author = $this->actingAsLevel(PermissionLevel::Viewer);
        $older = Media::create([
            'url' => 'http://localhost/storage/news/a.jpg',
            'path' => 'news/a.jpg',
            'original_name' => 'a.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'author_id' => $author->id,
        ]);
        $newer = Media::create([
            'url' => 'http://localhost/storage/news/b.jpg',
            'path' => 'news/b.jpg',
            'original_name' => 'b.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 200,
            'author_id' => $author->id,
        ]);

        $response = $this->getJson('/api/media');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.id', $newer->id);
        $response->assertJsonPath('data.1.id', $older->id);
        $response->assertJsonPath('data.0.originalName', 'b.jpg');
        $this->assertNotNull($response->json('data.0.author'));
    }

    public function test_destroy_forbidden_for_viewer(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Viewer);
        $media = Media::create([
            'url' => 'http://localhost/storage/news/a.jpg',
            'path' => 'news/a.jpg',
        ]);

        $response = $this->deleteJson("/api/media/{$media->id}");

        $response->assertStatus(403);
    }

    public function test_destroy_forbidden_for_editor(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);
        $media = Media::create([
            'url' => 'http://localhost/storage/news/a.jpg',
            'path' => 'news/a.jpg',
        ]);

        $response = $this->deleteJson("/api/media/{$media->id}");

        $response->assertStatus(403);
    }

    public function test_destroy_removes_media_record_and_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('news/a.jpg', 'fake-contents');
        $this->actingAsLevel(PermissionLevel::Administrator);
        $media = Media::create([
            'url' => 'http://localhost/storage/news/a.jpg',
            'path' => 'news/a.jpg',
        ]);

        $response = $this->deleteJson("/api/media/{$media->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing('news/a.jpg');
    }

    public function test_news_upload_image_registers_in_media_library(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/news/upload-image', [
            'image' => UploadedFile::fake()->image('news.png'),
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', [
            'original_name' => 'news.png',
        ]);
    }
}
