<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortActionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'icon' => $this->icon,
            'iconUrl' => $this->icon_url,
            'title' => $this->title,
            'description' => $this->description,
            'href' => $this->href,
            'external' => (bool) $this->external,
        ];
    }
}
