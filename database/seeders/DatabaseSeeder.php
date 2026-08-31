<?php

namespace Database\Seeders;

use App\Enums\PermissionLevel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'permission_level' => PermissionLevel::Supervisor,
        ]);

        // One account per permission tier for manual QA (password for all: "password").
        User::factory()->create([
            'name' => 'Viewer',
            'email' => 'viewer@example.com',
            'permission_level' => PermissionLevel::Viewer,
        ]);
        User::factory()->create([
            'name' => 'Redaktor',
            'email' => 'editor@example.com',
            'permission_level' => PermissionLevel::Editor,
        ]);
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'permission_level' => PermissionLevel::Administrator,
        ]);

        $this->call([
            ThemeSeeder::class,
            HeroSeeder::class,
            FooterConfigSeeder::class,
            NavItemSeeder::class,
            ShortActionItemSeeder::class,
            EventItemSeeder::class,
            NewsItemSeeder::class,
            InfoItemSeeder::class,
            ContentTopicSeeder::class,
            MassAndPastorSeeder::class,
            AssociationSeeder::class,
        ]);
    }
}
