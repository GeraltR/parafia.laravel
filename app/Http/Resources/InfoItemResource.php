<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InfoItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'validFrom' => $this->valid_from->toDateString(),
            'validTo' => $this->valid_to->toDateString(),
            'title' => $this->title,
            'shortInfo' => $this->short_info,
            'description' => $this->description,
            'image' => $this->image,
            'progressValue' => $this->progress_value,
            'progressDescription' => $this->progress_description,
            'information' => $this->information,
            'bannerText' => $this->banner_text,
            'bannerFont' => $this->banner_font,
            'bannerTextColor' => $this->banner_text_color,
            'bannerBgColor' => $this->banner_bg_color,
            'bannerDurationSeconds' => $this->banner_duration_seconds,
            'author' => $this->whenLoaded(
                'author',
                fn () => $this->author ? ['id' => $this->author->id, 'name' => $this->author->name] : null
            ),
        ];
    }
}
