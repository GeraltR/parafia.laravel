<?php

namespace Database\Seeders;

use App\Models\NewsItem;
use Illuminate\Database\Seeder;

class NewsItemSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/news.json')), true);

        foreach ($data as $item) {
            NewsItem::create([
                'date' => $item['date'],
                'title' => $item['title'],
                'excerpt' => $item['excerpt'],
                'image' => $item['image'],
            ]);
        }
    }
}
