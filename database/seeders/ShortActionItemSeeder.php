<?php

namespace Database\Seeders;

use App\Models\ShortActionItem;
use Illuminate\Database\Seeder;

class ShortActionItemSeeder extends Seeder
{
    public function run(): void
    {
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
