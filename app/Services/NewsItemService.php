<?php

namespace App\Services;

use App\Models\NewsItem;
use Illuminate\Http\UploadedFile;

class NewsItemService
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function create(array $data): NewsItem
    {
        return NewsItem::create([
            'date' => $data['date'],
            'title' => $data['title'],
            'excerpt' => $data['excerpt'],
            'image' => $data['image'] ?? null,
            'body' => $data['body'] ?? '',
            'show_image_on_full_content' => $data['showImageOnFullContent'] ?? true,
            'author_id' => $data['authorId'],
        ]);
    }

    public function update(NewsItem $newsItem, array $data): NewsItem
    {
        $newsItem->update([
            'date' => $data['date'],
            'title' => $data['title'],
            'excerpt' => $data['excerpt'],
            'image' => $data['image'] ?? null,
            'body' => $data['body'] ?? '',
            'show_image_on_full_content' => $data['showImageOnFullContent'] ?? true,
            'author_id' => $data['authorId'],
        ]);

        return $newsItem;
    }

    public function storeImage(UploadedFile $file, string $baseUrl, ?int $authorId = null): string
    {
        $path = $file->store('news', 'public');
        $url = rtrim($baseUrl, '/').'/storage/'.$path;

        $this->mediaService->record($file, $path, $url, $authorId);

        return $url;
    }
}
