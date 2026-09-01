<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ContentImageService
{
    public function storeImage(UploadedFile $file, string $baseUrl): string
    {
        $path = $file->store('content', 'public');

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }
}
