<?php

namespace App\Services;

use App\Models\SakramentyTopic;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class SakramentyTopicService
{
    private const MAX_TOPICS = 7;

    public function create(array $data): SakramentyTopic
    {
        $existingCount = SakramentyTopic::count();

        if ($existingCount >= self::MAX_TOPICS) {
            throw ValidationException::withMessages([
                'title' => 'Osiągnięto maksymalną liczbę tematów ('.self::MAX_TOPICS.') dla tej sekcji.',
            ]);
        }

        return SakramentyTopic::create([
            'icon_url' => $data['iconUrl'] ?? null,
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'visible_from' => $data['visibleFrom'] ?? null,
            'author_id' => $data['authorId'],
            'order' => $existingCount,
        ]);
    }

    public function update(SakramentyTopic $topic, array $data): SakramentyTopic
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
        $path = $file->store('content/sakramenty', 'public');

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }
}
