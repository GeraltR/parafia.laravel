<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParafiaTopicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'iconUrl' => $this->icon_url,
            'title' => $this->title,
            'content' => $this->content,
            'visibleFrom' => $this->visible_from?->toIso8601String(),
            'order' => $this->order,
            'author' => $this->whenLoaded(
                'author',
                fn () => $this->author ? ['id' => $this->author->id, 'name' => $this->author->name] : null
            ),
        ];
    }
}
