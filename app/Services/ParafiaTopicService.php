<?php

namespace App\Services;

use App\Models\ParafiaTopic;
use Illuminate\Http\UploadedFile;

class ParafiaTopicService
{
    public function create(array $data): ParafiaTopic
    {
        $existingCount = ParafiaTopic::count();

        return ParafiaTopic::create([
            'icon_url' => $data['iconUrl'] ?? null,
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'visible_from' => $data['visibleFrom'] ?? null,
            'author_id' => $data['authorId'],
            'order' => $existingCount,
        ]);
    }

    public function update(ParafiaTopic $topic, array $data): ParafiaTopic
    {
        $topic->update([
            'icon_url' => $data['iconUrl'] ?? null,
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'visible_from' => $data['visibleFrom'] ?? null,
            'author_id' => $data['authorId'],
        ]);

        return $topic;
    }

    public function storeImage(UploadedFile $file, string $baseUrl): string
    {
        $path = $file->store('content/parafia', 'public');

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }
}
