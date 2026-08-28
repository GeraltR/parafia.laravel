<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\Hero;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createHero(): Hero
    {
        $hero = Hero::create([
            'title' => 'Witaj',
            'title_width' => 10,
            'title_font' => '',
            'title_v_align' => 'center',
            'subtitle' => 'Podtytul',
            'subtitle_width' => 8,
            'subtitle_font' => '',
            'subtitle_v_align' => 'center',
            'keynote' => 'Motto',
            'keynote_width' => 10,
            'keynote_font' => '',
            'keynote_v_align' => 'center',
            'background_image' => '/img/bg.png',
        ]);

        $hero->buttons()->create([
            'label' => 'Msze Swiete',
            'href' => '#msze',
            'icon' => 'mass',
            'external' => false,
        ]);

        $hero->buttons()->create([
            'label' => 'Ogloszenia',
            'href' => '#aktualnosci',
            'icon' => 'announcements',
            'external' => false,
        ]);

        return $hero;
    }

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function validPayload(): array
    {
        return [
            'title' => 'Witaj',
            'titleWidth' => 10,
            'titleFont' => '',
            'titleVAlign' => 'center',
            'subtitle' => 'Podtytul',
            'subtitleWidth' => 8,
            'subtitleFont' => '',
            'subtitleVAlign' => 'center',
            'keynote' => 'Motto',
            'keynoteWidth' => 10,
            'keynoteFont' => '',
            'keynoteVAlign' => 'center',
            'backgroundImage' => '/img/bg.png',
            'buttons' => [],
        ];
    }

    public function test_show_returns_hero_with_buttons(): void
    {
        $this->createHero();

        $response = $this->getJson('/api/hero');

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Witaj');
        $response->assertJsonCount(2, 'data.buttons');
    }

    public function test_show_creates_default_row_when_missing(): void
    {
        $this->assertDatabaseCount('heroes', 0);

        $response = $this->getJson('/api/hero');

        $response->assertOk();
        $response->assertJsonCount(0, 'data.buttons');
        $this->assertDatabaseCount('heroes', 1);
    }

    public function test_update_requires_authentication(): void
    {
        $this->createHero();

        $response = $this->putJson('/api/hero', $this->validPayload());

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_viewer_and_editor(): void
    {
        $this->createHero();

        foreach ([PermissionLevel::Viewer, PermissionLevel::Editor] as $level) {
            $this->actingAsLevel($level);

            $response = $this->putJson('/api/hero', $this->validPayload());

            $response->assertStatus(403);
        }
    }

    public function test_update_allowed_for_administrator_and_supervisor(): void
    {
        $this->createHero();

        foreach ([PermissionLevel::Administrator, PermissionLevel::Supervisor] as $level) {
            $this->actingAsLevel($level);

            $response = $this->putJson('/api/hero', $this->validPayload());

            $response->assertOk();
        }
    }

    public function test_update_updates_hero_fields_and_syncs_buttons(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        $hero = $this->createHero();
        $keptButtonId = $hero->buttons()->where('label', 'Msze Swiete')->firstOrFail()->id;

        $payload = [
            'title' => 'Nowy tytul',
            'titleWidth' => 6,
            'titleFont' => '',
            'titleVAlign' => 'top',
            'subtitle' => 'Nowy podtytul',
            'subtitleWidth' => 6,
            'subtitleFont' => '',
            'subtitleVAlign' => 'bottom',
            'keynote' => 'Nowe motto',
            'keynoteWidth' => 6,
            'keynoteFont' => '',
            'keynoteVAlign' => 'center',
            'backgroundImage' => '/img/new-bg.png',
            'buttons' => [
                ['id' => $keptButtonId, 'label' => 'Msze Swiete (edycja)', 'href' => '#msze', 'icon' => 'mass', 'external' => false],
                ['label' => 'Transmisja', 'href' => '#live', 'icon' => 'live', 'external' => true],
            ],
        ];

        $response = $this->putJson('/api/hero', $payload);

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Nowy tytul');
        $response->assertJsonPath('data.backgroundImage', '/img/new-bg.png');
        $response->assertJsonCount(2, 'data.buttons');

        $this->assertDatabaseHas('hero_buttons', [
            'id' => $keptButtonId,
            'label' => 'Msze Swiete (edycja)',
        ]);
        $this->assertDatabaseMissing('hero_buttons', ['label' => 'Ogloszenia']);
        $this->assertDatabaseHas('hero_buttons', ['label' => 'Transmisja', 'external' => true]);
        $this->assertDatabaseCount('hero_buttons', 2);
    }

    public function test_update_allows_empty_title(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        $this->createHero();

        $payload = [
            'title' => '',
            'titleWidth' => 10,
            'titleFont' => '',
            'titleVAlign' => 'center',
            'subtitle' => '',
            'subtitleWidth' => 8,
            'subtitleFont' => '',
            'subtitleVAlign' => 'center',
            'keynote' => '',
            'keynoteWidth' => 10,
            'keynoteFont' => '',
            'keynoteVAlign' => 'center',
            'backgroundImage' => '/img/bg.png',
            'buttons' => [],
        ];

        $response = $this->putJson('/api/hero', $payload);

        $response->assertOk();
        $response->assertJsonPath('data.title', '');
    }

    public function test_update_persists_color_fields_and_null_clears_them(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        $hero = $this->createHero();
        $buttonId = $hero->buttons()->where('label', 'Msze Swiete')->firstOrFail()->id;

        $payload = [
            'title' => 'Witaj',
            'titleWidth' => 10,
            'titleFont' => '',
            'titleVAlign' => 'center',
            'titleColor' => '#ff0000',
            'subtitle' => 'Podtytul',
            'subtitleWidth' => 8,
            'subtitleFont' => '',
            'subtitleVAlign' => 'center',
            'subtitleColor' => '#00ff00',
            'keynote' => 'Motto',
            'keynoteWidth' => 10,
            'keynoteFont' => '',
            'keynoteVAlign' => 'center',
            'backgroundImage' => '/img/bg.png',
            'buttons' => [
                [
                    'id' => $buttonId,
                    'label' => 'Msze Swiete',
                    'href' => '#msze',
                    'icon' => 'mass',
                    'external' => false,
                    'textColor' => '#111111',
                    'textColorHover' => '#222222',
                    'bgColor' => '#333333',
                    'bgColorHover' => '#444444',
                ],
            ],
        ];

        $response = $this->putJson('/api/hero', $payload);

        $response->assertOk();
        $response->assertJsonPath('data.titleColor', '#ff0000');
        $response->assertJsonPath('data.subtitleColor', '#00ff00');
        $response->assertJsonPath('data.buttons.0.textColor', '#111111');
        $response->assertJsonPath('data.buttons.0.textColorHover', '#222222');
        $response->assertJsonPath('data.buttons.0.bgColor', '#333333');
        $response->assertJsonPath('data.buttons.0.bgColorHover', '#444444');

        $payload['titleColor'] = null;
        $payload['buttons'][0]['textColor'] = null;

        $response = $this->putJson('/api/hero', $payload);

        $response->assertOk();
        $response->assertJsonPath('data.titleColor', null);
        $response->assertJsonPath('data.buttons.0.textColor', null);
    }

    public function test_update_requires_valid_data(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        $this->createHero();

        $response = $this->putJson('/api/hero', [
            'title' => 'Tylko tytul',
        ]);

        $response->assertStatus(422);
    }

    public function test_upload_background_image_requires_write_permission(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/hero/background-image', [
            'image' => UploadedFile::fake()->image('bg.jpg'),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_background_image_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->postJson('/api/hero/background-image', [
            'image' => UploadedFile::fake()->image('bg.jpg'),
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['url']);

        $url = $response->json('url');
        $this->assertStringContainsString('/storage/hero/', $url);

        $path = 'hero/'.basename($url);
        Storage::disk('public')->assertExists($path);
    }

    public function test_upload_background_image_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->postJson('/api/hero/background-image', [
            'image' => UploadedFile::fake()->create('document.pdf', 10),
        ]);

        $response->assertStatus(422);
    }
}
