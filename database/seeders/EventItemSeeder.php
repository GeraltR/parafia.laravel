<?php

namespace Database\Seeders;

use App\Models\EventItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventItemSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/events.json')), true);

        foreach ($data as $item) {
            EventItem::create([
                'date' => $item['date'],
                'time' => trim(Str::afterLast($item['time'], ',')),
                'title' => $item['title'],
                'description' => $item['description'],
            ]);
        }
    }
}
