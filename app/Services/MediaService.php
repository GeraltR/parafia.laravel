<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function record(UploadedFile $file, string $path, string $url, ?int $authorId): Media
    {
        return Media::create([
            'url' => $url,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'author_id' => $authorId,
        ]);
    }

    public function delete(Media $media): void
    {
        Storage::disk('public')->delete($media->path);
        $media->delete();
    }
}
