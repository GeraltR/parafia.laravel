<?php

namespace App\Services;

use App\Models\EventItem;

class EventItemService
{
    public function create(array $data): EventItem
    {
        return EventItem::create([
            'date' => $data['date'],
            'time' => $data['time'],
            'title' => $data['title'],
            'description' => $data['description'],
            'body' => $data['body'] ?? '',
            'author_id' => $data['authorId'],
        ]);
    }

    public function update(EventItem $eventItem, array $data): EventItem
    {
        $eventItem->update([
            'date' => $data['date'],
            'time' => $data['time'],
            'title' => $data['title'],
            'description' => $data['description'],
            'body' => $data['body'] ?? '',
            'author_id' => $data['authorId'],
        ]);

        return $eventItem;
    }
}
