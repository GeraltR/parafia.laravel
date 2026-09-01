<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentImageControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_upload_requires_authentication(): void
    {
        $response = $this->postJson('/api/content-images', [
            'image' => UploadedFile::fake()->image('pic.jpg'),
        ]);

        $response->assertStatus(401);
    }

    public function test_upload_requires_write_permission(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/content-images', [
            'image' => UploadedFile::fake()->image('pic.jpg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/content-images', [
            'image' => UploadedFile::fake()->image('pic.jpg'),
        ]);

        $response->assertOk();
        $url = $response->json('url');
        $this->assertStringContainsString('/storage/content/', $url);
        Storage::disk('public')->assertExists('content/'.basename($url));
    }

    public function test_upload_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/content-images', [
            'image' => UploadedFile::fake()->create('document.pdf', 10),
        ]);

        $response->assertStatus(422);
    }
}
