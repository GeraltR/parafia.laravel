<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\LiturgiaTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LiturgiaTopicControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function validPayload(User $author, array $overrides = []): array
    {
        return array_merge([
            'iconUrl' => null,
            'title' => 'Adwent',
            'content' => '<p>Treść</p>',
            'visibleFrom' => null,
            'authorId' => $author->id,
        ], $overrides);
    }

    public function test_index_returns_only_visible_topics_ordered(): void
    {
        $author = User::factory()->create();
        LiturgiaTopic::create(['title' => 'B', 'author_id' => $author->id, 'order' => 1]);
        LiturgiaTopic::create(['title' => 'A', 'author_id' => $author->id, 'order' => 0]);
        LiturgiaTopic::create([
            'title' => 'Przyszła', 'author_id' => $author->id, 'order' => 2,
            'visible_from' => now()->addDay(),
        ]);

        $response = $this->getJson('/api/liturgia-topics');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.title', 'A');
        $response->assertJsonPath('data.1.title', 'B');
    }

    public function test_manage_requires_authentication(): void
    {
        $response = $this->getJson('/api/liturgia-topics/manage');

        $response->assertStatus(401);
    }

    public function test_manage_returns_all_topics_including_future(): void
    {
        $author = $this->actingAsLevel(PermissionLevel::Viewer);
        LiturgiaTopic::create([
            'title' => 'Przyszła', 'author_id' => $author->id, 'order' => 0,
            'visible_from' => now()->addDay(),
        ]);

        $response = $this->getJson('/api/liturgia-topics/manage');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_store_requires_authentication(): void
    {
        $author = User::factory()->create();

        $response = $this->postJson('/api/liturgia-topics', $this->validPayload($author));

        $response->assertStatus(401);
    }

    public function test_store_forbidden_for_viewer(): void
    {
        $author = $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/liturgia-topics', $this->validPayload($author));

        $response->assertStatus(403);
    }

    public function test_store_allowed_for_editor_administrator_and_supervisor(): void
    {
        foreach ([PermissionLevel::Editor, PermissionLevel::Administrator, PermissionLevel::Supervisor] as $level) {
            $author = $this->actingAsLevel($level);

            $response = $this->postJson('/api/liturgia-topics', $this->validPayload($author, ['title' => "Temat {$level->value}"]));

            $response->assertStatus(201);
        }
    }

    public function test_store_requires_valid_data(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/liturgia-topics', []);

        $response->assertStatus(422);
    }

    public function test_store_does_not_limit_number_of_topics(): void
    {
        $author = $this->actingAsLevel(PermissionLevel::Editor);

        foreach (range(1, 8) as $i) {
            LiturgiaTopic::create(['title' => "Temat {$i}", 'author_id' => $author->id, 'order' => $i]);
        }

        $response = $this->postJson('/api/liturgia-topics', $this->validPayload($author, ['title' => 'Dziewiąty temat']));

        $response->assertStatus(201);
    }

    public function test_store_sets_incremental_order(): void
    {
        $author = $this->actingAsLevel(PermissionLevel::Editor);
        LiturgiaTopic::create(['title' => 'Pierwszy', 'author_id' => $author->id, 'order' => 0]);

        $response = $this->postJson('/api/liturgia-topics', $this->validPayload($author, ['title' => 'Drugi']));

        $response->assertStatus(201);
        $response->assertJsonPath('data.order', 1);
    }

    public function test_update_modifies_topic(): void
    {
        $author = $this->actingAsLevel(PermissionLevel::Administrator);
        $topic = LiturgiaTopic::create(['title' => 'Stary', 'author_id' => $author->id, 'order' => 0]);

        $response = $this->putJson("/api/liturgia-topics/{$topic->id}", $this->validPayload($author, ['title' => 'Nowy']));

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Nowy');
        $this->assertDatabaseHas('liturgia_topics', ['id' => $topic->id, 'title' => 'Nowy']);
    }

    public function test_update_forbidden_for_viewer(): void
    {
        $author = User::factory()->create();
        $topic = LiturgiaTopic::create(['title' => 'Stary', 'author_id' => $author->id, 'order' => 0]);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->putJson("/api/liturgia-topics/{$topic->id}", $this->validPayload($author));

        $response->assertStatus(403);
    }

    public function test_destroy_removes_topic(): void
    {
        $author = $this->actingAsLevel(PermissionLevel::Editor);
        $topic = LiturgiaTopic::create(['title' => 'Do usunięcia', 'author_id' => $author->id, 'order' => 0]);

        $response = $this->deleteJson("/api/liturgia-topics/{$topic->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('liturgia_topics', ['id' => $topic->id]);
    }

    public function test_destroy_forbidden_for_viewer(): void
    {
        $author = User::factory()->create();
        $topic = LiturgiaTopic::create(['title' => 'X', 'author_id' => $author->id, 'order' => 0]);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->deleteJson("/api/liturgia-topics/{$topic->id}");

        $response->assertStatus(403);
    }

    public function test_upload_image_requires_write_permission(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/liturgia-topics/upload-image', [
            'image' => UploadedFile::fake()->image('pic.jpg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_image_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/liturgia-topics/upload-image', [
            'image' => UploadedFile::fake()->image('pic.jpg'),
        ]);

        $response->assertOk();
        $url = $response->json('url');
        $this->assertStringContainsString('/storage/content/liturgia/', $url);
        Storage::disk('public')->assertExists('content/liturgia/'.basename($url));
    }

    public function test_upload_image_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/liturgia-topics/upload-image', [
            'image' => UploadedFile::fake()->create('document.pdf', 10),
        ]);

        $response->assertStatus(422);
    }
}
