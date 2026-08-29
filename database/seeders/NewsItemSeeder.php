<?php

namespace Database\Seeders;

use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsItemSeeder extends Seeder
{
    public function run(): void
    {
        if (NewsItem::query()->exists()) {
            return;
        }

        $author = User::where('email', 'test@example.com')->first() ?? User::first();
        $data = json_decode(file_get_contents(database_path('seeders/data/news.json')), true);

        foreach ($data as $item) {
            NewsItem::create([
                'date' => $item['date'],
                'title' => $item['title'],
                'excerpt' => $item['excerpt'],
                'image' => $item['image'],
                'author_id' => $author?->id,
            ]);
        }
    }
}
