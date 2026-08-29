<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\EventItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventItemControllerTest extends TestCase
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
            'date' => now()->addDays(5)->toDateString(),
            'time' => '10:00',
            'title' => 'Nowe wydarzenie',
            'description' => 'Opis wydarzenia',
            'body' => '<p>Pełna treść</p>',
            'authorId' => $author->id,
        ];
    }

    public function test_index_returns_latest_four_ordered_by_date_desc_regardless_of_today(): void
    {
        EventItem::create(['date' => now()->addDays(2), 'time' => '10:00', 'title' => 'Przyszłe', 'description' => 'x']);
        for ($i = 0; $i < 6; $i++) {
            EventItem::create(['date' => now()->subDays($i + 1), 'time' => '10:00', 'title' => "Wydarzenie {$i}", 'description' => 'x']);
        }

        $response = $this->getJson('/api/events');

        $response->assertOk();
        $response->assertJsonCount(4, 'data');
        $response->assertJsonPath('data.0.title', 'Przyszłe');
        $response->assertJsonPath('data.1.title', 'Wydarzenie 0');
    }

    public function test_manage_requires_authentication(): void
    {
        $response = $this->getJson('/api/events/manage');

        $response->assertStatus(401);
    }

    public function test_manage_returns_all_events_including_past(): void
    {
        EventItem::create(['date' => now()->subDays(2), 'time' => '10:00', 'title' => 'Przeszłe', 'description' => 'x']);
        EventItem::create(['date' => now()->addDays(2), 'time' => '10:00', 'title' => 'Przyszłe', 'description' => 'x']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/events/manage');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->postJson('/api/events', $this->payload());

        $response->assertStatus(401);
    }

    public function test_store_forbidden_for_viewer(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->postJson('/api/events', $this->payload());

        $response->assertStatus(403);
    }

    public function test_store_allowed_for_editor(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->postJson('/api/events', $this->payload());

        $response->assertCreated();
        $response->assertJsonPath('data.title', 'Nowe wydarzenie');
        $response->assertJsonPath('data.body', '<p>Pełna treść</p>');
        $this->assertNotNull($response->json('data.author'));
    }

    public function test_store_rejects_invalid_time_format(): void
    {
        $this->actingAsLevel(PermissionLevel::Editor);

        $payload = $this->payload();
        $payload['time'] = 'not-a-time';

        $response = $this->postJson('/api/events', $payload);

        $response->assertStatus(422);
    }

    public function test_update_persists_changes(): void
    {
        $event = EventItem::create(['date' => now()->addDays(3), 'time' => '10:00', 'title' => 'Stare', 'description' => 'x']);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->putJson("/api/events/{$event->id}", $this->payload());

        $response->assertOk();
        $response->assertJsonPath('data.title', 'Nowe wydarzenie');
    }

    public function test_destroy_forbidden_for_viewer(): void
    {
        $event = EventItem::create(['date' => now()->addDays(3), 'time' => '10:00', 'title' => 'X', 'description' => 'x']);
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(403);
    }

    public function test_destroy_allowed_for_editor(): void
    {
        $event = EventItem::create(['date' => now()->addDays(3), 'time' => '10:00', 'title' => 'X', 'description' => 'x']);
        $this->actingAsLevel(PermissionLevel::Editor);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('event_items', ['id' => $event->id]);
    }
}
