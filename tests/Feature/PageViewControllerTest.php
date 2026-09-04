<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\PageView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageViewControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_store_is_public_and_records_page_view(): void
    {
        $this->assertDatabaseCount('page_views', 0);

        $response = $this->postJson('/api/page-views', [
            'path' => '/',
            'referrer' => 'https://google.com',
        ], ['REMOTE_ADDR' => '203.0.113.5']);

        $response->assertStatus(204);
        $this->assertDatabaseCount('page_views', 1);
    }

    public function test_store_requires_path(): void
    {
        $response = $this->postJson('/api/page-views', []);

        $response->assertStatus(422);
    }

    public function test_store_never_persists_raw_ip(): void
    {
        $this->postJson('/api/page-views', ['path' => '/'], ['REMOTE_ADDR' => '203.0.113.5']);

        $pageView = PageView::first();

        $this->assertNotNull($pageView);
        $this->assertStringNotContainsString('203.0.113.5', $pageView->visitor_hash);
        $this->assertSame(64, strlen($pageView->visitor_hash));
    }

    public function test_summary_requires_authentication(): void
    {
        $response = $this->getJson('/api/page-views/summary');

        $response->assertStatus(401);
    }

    public function test_summary_returns_aggregated_data(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        PageView::create(['path' => '/', 'visitor_hash' => 'hash-a']);
        PageView::create(['path' => '/', 'visitor_hash' => 'hash-b']);
        PageView::create(['path' => '/parafia', 'visitor_hash' => 'hash-a']);

        $response = $this->getJson('/api/page-views/summary?days=7');

        $response->assertOk();
        $response->assertJsonPath('totalViews', 3);
        $response->assertJsonPath('totalUniqueVisitors', 2);
        $response->assertJsonPath('topPaths.0.path', '/');
        $response->assertJsonPath('topPaths.0.views', 2);
        $response->assertJsonCount(7, 'daily');
    }

    public function test_summary_clamps_days_parameter(): void
    {
        $this->actingAsLevel(PermissionLevel::Viewer);

        $response = $this->getJson('/api/page-views/summary?days=9999');

        $response->assertOk();
        $response->assertJsonPath('days', 365);
        $response->assertJsonCount(365, 'daily');
    }
}
