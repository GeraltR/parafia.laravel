<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortActionsConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'titleFont' => $this->title_font,
            'titleSize' => $this->title_size,
            'titleColor' => $this->title_color,
            'subtitleFont' => $this->subtitle_font,
            'subtitleSize' => $this->subtitle_size,
            'subtitleColor' => $this->subtitle_color,
            'bgColor' => $this->bg_color,
            'bgColorHover' => $this->bg_color_hover,
        ];
    }
}
