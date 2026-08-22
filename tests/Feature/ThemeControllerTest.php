<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createTheme(): Theme
    {
        return Theme::create([
            'primary_color' => '#111111',
            'secondary_color' => '#222222',
            'font_heading' => 'Merriweather',
            'font_body' => 'Inter',
            'title' => 'Parafia',
            'subtitle' => 'Podtytul',
        ]);
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
            'primaryColor' => '#333333',
            'secondaryColor' => '#444444',
            'fontHeading' => 'Merriweather',
            'fontBody' => 'Inter',
            'title' => 'Nowy tytul',
            'subtitle' => 'Nowy podtytul',
        ];
    }

    public function test_show_is_public(): void
    {
        $this->createTheme();

        $response = $this->getJson('/api/theme');

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Parafia');
    }

    public function test_update_requires_authentication(): void
    {
        $this->createTheme();

        $response = $this->putJson('/api/theme', $this->validPayload());

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_viewer_and_editor(): void
    {
        $this->createTheme();

        foreach ([PermissionLevel::Viewer, PermissionLevel::Editor] as $level) {
            $this->actingAsLevel($level);

            $response = $this->putJson('/api/theme', $this->validPayload());

            $response->assertStatus(403);
        }
    }

    public function test_update_allowed_for_administrator_and_supervisor(): void
    {
        $this->createTheme();

        foreach ([PermissionLevel::Administrator, PermissionLevel::Supervisor] as $level) {
            $this->actingAsLevel($level);

            $response = $this->putJson('/api/theme', $this->validPayload());

            $response->assertOk();
            $response->assertJsonPath('data.title', 'Nowy tytul');
        }
    }
}
