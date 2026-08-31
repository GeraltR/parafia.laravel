<?php

namespace Database\Seeders;

use App\Models\InfoItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class InfoItemSeeder extends Seeder
{
    public function run(): void
    {
        if (InfoItem::query()->exists()) {
            return;
        }

        $author = User::where('email', 'test@example.com')->first() ?? User::first();
        $data = json_decode(file_get_contents(database_path('seeders/data/infoItems.json')), true);

        foreach ($data as $item) {
            InfoItem::create([
                'valid_from' => now()->addDays($item['validFromDaysOffset'])->toDateString(),
                'valid_to' => now()->addDays($item['validToDaysOffset'])->toDateString(),
                'title' => $item['title'],
                'short_info' => $item['shortInfo'],
                'description' => $item['description'],
                'image' => $item['image'],
                'progress_value' => $item['progressValue'],
                'progress_description' => $item['progressDescription'],
                'information' => $item['information'] ?? null,
                'author_id' => $author?->id,
            ]);
        }
    }
}
