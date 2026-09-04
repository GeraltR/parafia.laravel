<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'originalName' => $this->original_name,
            'mimeType' => $this->mime_type,
            'size' => $this->size,
            'createdAt' => $this->created_at?->toIso8601String(),
            'author' => $this->whenLoaded(
                'author',
                fn () => $this->author ? ['id' => $this->author->id, 'name' => $this->author->name] : null
            ),
        ];
    }
}
