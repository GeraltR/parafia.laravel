<?php

namespace Database\Seeders;

use App\Models\ShortActionItem;
use App\Models\ShortActionsConfig;
use Illuminate\Database\Seeder;

class ShortActionItemSeeder extends Seeder
{
    public function run(): void
    {
        ShortActionsConfig::create([
            'title_size' => '0.84rem',
            'title_color' => '#1a365d',
            'subtitle_size' => '0.62rem',
            'subtitle_color' => '#5a6b7d',
            'bg_color' => '#ffffff',
            'bg_color_hover' => '#f4f6f8',
        ]);

        $data = json_decode(file_get_contents(database_path('seeders/data/shortActions.json')), true);

        foreach ($data as $item) {
            ShortActionItem::create([
                'icon' => $item['icon'],
                'title' => $item['title'],
                'description' => $item['description'],
                'href' => $item['href'],
                'external' => $item['external'] ?? false,
            ]);
        }
    }
}
