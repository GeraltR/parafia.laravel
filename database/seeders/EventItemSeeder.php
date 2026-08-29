<?php

namespace Database\Seeders;

use App\Models\EventItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventItemSeeder extends Seeder
{
    public function run(): void
    {
        if (EventItem::query()->exists()) {
            return;
        }

        $author = User::where('email', 'test@example.com')->first() ?? User::first();
        $data = json_decode(file_get_contents(database_path('seeders/data/events.json')), true);

        foreach ($data as $item) {
            EventItem::create([
                'date' => now()->addDays($item['daysOffset'])->toDateString(),
                'time' => $item['time'],
                'title' => $item['title'],
                'description' => $item['description'],
                'author_id' => $author?->id,
            ]);
        }
    }
}
