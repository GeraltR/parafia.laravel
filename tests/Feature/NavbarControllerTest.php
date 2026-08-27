<?php

namespace Tests\Feature;

use App\Enums\PermissionLevel;
use App\Models\NavItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsLevel(PermissionLevel $level): User
    {
        $user = User::factory()->create(['permission_level' => $level]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function createTree(): array
    {
        $home = NavItem::create(['label' => 'Strona główna', 'href' => '/', 'order' => 0]);
        $sakramenty = NavItem::create(['label' => 'Sakramenty', 'href' => '#sakramenty', 'order' => 1]);
        $chrzest = NavItem::create([
            'parent_id' => $sakramenty->id,
            'label' => 'Chrzest',
            'href' => '#chrzest',
            'order' => 0,
        ]);

        return compact('home', 'sakramenty', 'chrzest');
    }

    private function validPayload(): array
    {
        return [
            'items' => [
                ['label' => 'Strona główna', 'href' => '/'],
                [
                    'label' => 'Sakramenty',
                    'href' => '#sakramenty',
                    'children' => [
                        ['label' => 'Chrzest', 'href' => '#chrzest'],
                    ],
                ],
            ],
        ];
    }

    public function test_show_is_public_and_returns_nested_tree(): void
    {
        $this->createTree();

        $response = $this->getJson('/api/navbar');

        $response->assertOk();
        $response->assertJsonCount(2, 'data.items');
        $response->assertJsonPath('data.items.1.label', 'Sakramenty');
        $response->assertJsonCount(1, 'data.items.1.children');
        $response->assertJsonPath('data.items.1.children.0.label', 'Chrzest');
    }

    public function test_show_does_not_include_children_as_top_level_items(): void
    {
        $this->createTree();

        $response = $this->getJson('/api/navbar');

        $topLevelLabels = array_column($response->json('data.items'), 'label');
        $this->assertNotContains('Chrzest', $topLevelLabels);
        $response->assertJsonPath('data.items.1.children.0.label', 'Chrzest');
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/navbar', $this->validPayload());

        $response->assertStatus(401);
    }

    public function test_update_forbidden_for_viewer_and_editor(): void
    {
        foreach ([PermissionLevel::Viewer, PermissionLevel::Editor] as $level) {
            $this->actingAsLevel($level);

            $response = $this->putJson('/api/navbar', $this->validPayload());

            $response->assertStatus(403);
        }
    }

    public function test_update_allowed_for_administrator_and_supervisor(): void
    {
        foreach ([PermissionLevel::Administrator, PermissionLevel::Supervisor] as $level) {
            $this->actingAsLevel($level);

            $response = $this->putJson('/api/navbar', $this->validPayload());

            $response->assertOk();
        }
    }

    public function test_update_creates_top_level_items_and_children(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->putJson('/api/navbar', $this->validPayload());

        $response->assertOk();
        $response->assertJsonCount(2, 'data.items');
        $response->assertJsonPath('data.items.1.children.0.label', 'Chrzest');
        $this->assertDatabaseCount('nav_items', 3);
    }

    public function test_update_syncs_existing_tree_by_id(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        ['sakramenty' => $sakramenty, 'chrzest' => $chrzest, 'home' => $home] = $this->createTree();

        $payload = [
            'items' => [
                ['id' => $home->id, 'label' => 'Strona główna', 'href' => '/'],
                [
                    'id' => $sakramenty->id,
                    'label' => 'Sakramenty (edycja)',
                    'href' => '#sakramenty',
                    'children' => [
                        ['id' => $chrzest->id, 'label' => 'Chrzest', 'href' => '#chrzest'],
                        ['label' => 'Bierzmowanie', 'href' => '#bierzmowanie'],
                    ],
                ],
            ],
        ];

        $response = $this->putJson('/api/navbar', $payload);

        $response->assertOk();
        $this->assertDatabaseHas('nav_items', ['id' => $sakramenty->id, 'label' => 'Sakramenty (edycja)']);
        $this->assertDatabaseCount('nav_items', 4);
    }

    public function test_update_removes_items_and_children_missing_from_payload(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        $this->createTree();

        $response = $this->putJson('/api/navbar', [
            'items' => [
                ['label' => 'Strona główna', 'href' => '/'],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseCount('nav_items', 1);
        $this->assertDatabaseMissing('nav_items', ['label' => 'Sakramenty']);
        $this->assertDatabaseMissing('nav_items', ['label' => 'Chrzest']);
    }

    public function test_update_requires_valid_data(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);

        $response = $this->putJson('/api/navbar', [
            'items' => [
                ['label' => 'Bez hrefa'],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_show_exposes_is_locked(): void
    {
        NavItem::create(['label' => 'Strona główna', 'href' => '/', 'order' => 0, 'is_locked' => true]);
        NavItem::create(['label' => 'Parafia', 'href' => '/kroniki', 'order' => 1]);

        $response = $this->getJson('/api/navbar');

        $response->assertJsonPath('data.items.0.isLocked', true);
        $response->assertJsonPath('data.items.1.isLocked', false);
    }

    public function test_update_does_not_delete_locked_top_level_item_missing_from_payload(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        $locked = NavItem::create(['label' => 'Kontakt', 'href' => '#kontakt', 'order' => 0, 'is_locked' => true]);
        $other = NavItem::create(['label' => 'Parafia', 'href' => '/kroniki', 'order' => 1]);

        $response = $this->putJson('/api/navbar', [
            'items' => [
                ['id' => $other->id, 'label' => 'Parafia', 'href' => '/kroniki'],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('nav_items', ['id' => $locked->id]);
    }

    public function test_update_does_not_delete_locked_child_missing_from_payload(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        $parent = NavItem::create(['label' => 'Sakramenty', 'href' => '#sakramenty', 'order' => 0]);
        $lockedChild = NavItem::create([
            'parent_id' => $parent->id,
            'label' => 'Chrzest',
            'href' => '#chrzest',
            'order' => 0,
            'is_locked' => true,
        ]);

        $response = $this->putJson('/api/navbar', [
            'items' => [
                ['id' => $parent->id, 'label' => 'Sakramenty', 'href' => '#sakramenty', 'children' => []],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('nav_items', ['id' => $lockedChild->id]);
    }

    public function test_update_ignores_client_supplied_is_locked(): void
    {
        $this->actingAsLevel(PermissionLevel::Administrator);
        $item = NavItem::create(['label' => 'Kontakt', 'href' => '#kontakt', 'order' => 0, 'is_locked' => true]);

        $response = $this->putJson('/api/navbar', [
            'items' => [
                ['id' => $item->id, 'label' => 'Kontakt', 'href' => '#kontakt', 'isLocked' => false],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('nav_items', ['id' => $item->id, 'is_locked' => true]);
    }
}
