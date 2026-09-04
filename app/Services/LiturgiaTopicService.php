<?php

namespace App\Services;

use App\Models\LiturgiaTopic;
use Illuminate\Http\UploadedFile;

class LiturgiaTopicService
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function create(array $data): LiturgiaTopic
    {
        $existingCount = LiturgiaTopic::count();

        return LiturgiaTopic::create([
            'icon_url' => $data['iconUrl'] ?? null,
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'visible_from' => $data['visibleFrom'] ?? null,
            'author_id' => $data['authorId'],
            'order' => $existingCount,
        ]);
    }

    public function update(LiturgiaTopic $topic, array $data): LiturgiaTopic
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

    public function storeImage(UploadedFile $file, string $baseUrl, ?int $authorId = null): string
    {
        $path = $file->store('content/liturgia', 'public');
        $url = rtrim($baseUrl, '/').'/storage/'.$path;

        $this->mediaService->record($file, $path, $url, $authorId);

        return $url;
    }
}
