<?php

namespace Tests\Feature;

use App\Models\Hero;
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

    public function test_show_returns_hero_with_buttons(): void
    {
        $this->createHero();

        $response = $this->getJson('/api/hero');

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Witaj');
        $response->assertJsonCount(2, 'data.buttons');
    }

    public function test_update_updates_hero_fields_and_syncs_buttons(): void
    {
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

    public function test_update_requires_valid_data(): void
    {
        $this->createHero();

        $response = $this->putJson('/api/hero', [
            'title' => 'Tylko tytul',
        ]);

        $response->assertStatus(422);
    }

    public function test_upload_background_image_stores_file_and_returns_url(): void
    {
        Storage::fake('public');

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

        $response = $this->postJson('/api/hero/background-image', [
            'image' => UploadedFile::fake()->create('document.pdf', 10),
        ]);

        $response->assertStatus(422);
    }
}
