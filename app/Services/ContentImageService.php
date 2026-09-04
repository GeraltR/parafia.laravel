<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ContentImageService
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function storeImage(UploadedFile $file, string $baseUrl, ?int $authorId = null): string
    {
        $path = $file->store('content', 'public');
        $url = rtrim($baseUrl, '/').'/storage/'.$path;

        $this->mediaService->record($file, $path, $url, $authorId);

        return $url;
    }
}
