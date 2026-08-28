<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\MassAndPastorSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MassAndPastorControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function validPayload(): array
    {
        return [
            'config' => [
                'positionColor' => '#111111',
                'nameColor' => '#222222',
            ],
            'massTimes' => [
                ['label' => 'Niedziela', 'hours' => '9:00, 11:00'],
            ],
            'pastors' => [
                ['position' => 'Proboszcz', 'fullName' => 'ks. Jan Kowalski', 'duties' => '<p>Duszpasterstwo</p>'],
            ],
        ];
    }

    public function test_show_is_public(): void
    {
        $section = MassAndPastorSection::create([]);
        $section->massTimes()->create(['label' => 'Niedziela', 'hours' => '9:00', 'order' => 0]);
        $section->pastors()->create(['position' => 'Proboszcz', 'full_name' => 'ks. Test', 'order' => 0]);

        $response = $this->getJson('/api/mass-and-pastor');

        $response->assertOk();
        $response->assertJsonCount(1, 'data.massTimes');
        $response->assertJsonCount(1, 'data.pastors');
    }

    public function test_show_creates_default_row_when_missing(): void
    {
        $this->assertDatabaseCount('mass_and_pastor_sections', 0);

        $response = $this->getJson('/api/mass-and-pastor');

        $response->assertOk();
        $response->assertJsonCount(0, 'data.massTimes');
        $response->assertJsonCount(0, 'data.pastors');
        $this->assertDatabaseCount('mass_and_pastor_sections', 1);
    }

    public function test_update_requires_authentication(): void
    {
        MassAndPastorSection::create([]);

        $response = $this->putJson('/api/mass-and-pastor', $this->validPayload());

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_viewer(): void
    {
        MassAndPastorSection::create([]);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->putJson('/api/mass-and-pastor', $this->validPayload());

        $response->assertStatus(403);
    }

    public function test_update_allowed_for_editor_creates_children(): void
    {
        MassAndPastorSection::create([]);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/mass-and-pastor', $this->validPayload());

        $response->assertOk();
        $response->assertJsonCount(1, 'data.massTimes');
        $response->assertJsonCount(1, 'data.pastors');
        $response->assertJsonPath('data.config.positionColor', '#111111');
        $response->assertJsonPath('data.pastors.0.isActive', true);
        $this->assertDatabaseCount('mass_times', 1);
        $this->assertDatabaseCount('pastors', 1);
    }

    public function test_update_persists_pastor_active_flag(): void
    {
        MassAndPastorSection::create([]);
        $this->actingAsLevel(PermissionLevel::Editor);

        $payload = $this->validPayload();
        $payload['pastors'][0]['isActive'] = false;

        $response = $this->putJson('/api/mass-and-pastor', $payload);

        $response->assertOk();
        $response->assertJsonPath('data.pastors.0.isActive', false);
        $this->assertDatabaseHas('pastors', ['full_name' => 'ks. Jan Kowalski', 'active' => false]);
    }

    public function test_update_syncs_existing_children_by_id(): void
    {
        $section = MassAndPastorSection::create([]);
        $massTime = $section->massTimes()->create(['label' => 'Stary', 'hours' => '1:00', 'order' => 0]);
        $pastor = $section->pastors()->create(['position' => 'Stary', 'full_name' => 'Stary', 'order' => 0]);
        $this->actingAsLevel(PermissionLevel::Editor);

        $payload = [
            'config' => [],
            'massTimes' => [
                ['id' => $massTime->id, 'label' => 'Zaktualizowany', 'hours' => '2:00'],
                ['label' => 'Nowy wpis', 'hours' => '3:00'],
            ],
            'pastors' => [
                ['id' => $pastor->id, 'position' => 'Nowy', 'fullName' => 'Nowy', 'duties' => ''],
            ],
        ];

        $response = $this->putJson('/api/mass-and-pastor', $payload);

        $response->assertOk();
        $this->assertDatabaseHas('mass_times', ['id' => $massTime->id, 'label' => 'Zaktualizowany']);
        $this->assertDatabaseCount('mass_times', 2);
        $this->assertDatabaseCount('pastors', 1);
    }

    public function test_update_removes_children_missing_from_payload(): void
    {
        $section = MassAndPastorSection::create([]);
        $section->massTimes()->create(['label' => 'Do usunięcia', 'hours' => '1:00', 'order' => 0]);
        $section->pastors()->create(['position' => 'Do usunięcia', 'full_name' => 'X', 'order' => 0]);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson('/api/mass-and-pastor', [
            'config' => [],
            'massTimes' => [],
            'pastors' => [],
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('mass_times', 0);
        $this->assertDatabaseCount('pastors', 0);
    }

    public function test_upload_photo_requires_write_permission(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/mass-and-pastor/upload-photo', [
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_photo_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/mass-and-pastor/upload-photo', [
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $response->assertOk();
        $this->assertStringContainsString('/storage/pastors/', $response->json('url'));
    }
}
