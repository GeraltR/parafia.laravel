<?php

namespace App\Services;

use App\Enums\ContentPageSlug;
use App\Models\ContentTopic;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ContentTopicService
{
    public function create(array $data): ContentTopic
    {
        $page = ContentPageSlug::from($data['page']);

        $existingCount = ContentTopic::where('page', $page->value)->count();
        $maxTopics = $page->maxTopics();

        if ($maxTopics !== null && $existingCount >= $maxTopics) {
            throw ValidationException::withMessages([
                'page' => "Osiągnięto maksymalną liczbę tematów ({$maxTopics}) dla tej sekcji.",
            ]);
        }

        return ContentTopic::create([
            'page' => $page->value,
            'icon_url' => $data['iconUrl'] ?? null,
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'visible_from' => $data['visibleFrom'] ?? null,
            'author_id' => $data['authorId'],
            'order' => $existingCount,
        ]);
    }

    public function update(ContentTopic $topic, array $data): ContentTopic
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
        $path = $file->store('content', 'public');

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }
}
