<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'titleWidth' => $this->title_width,
            'titleFont' => $this->title_font,
            'titleVAlign' => $this->title_v_align,
            'subtitle' => $this->subtitle,
            'subtitleWidth' => $this->subtitle_width,
            'subtitleFont' => $this->subtitle_font,
            'subtitleVAlign' => $this->subtitle_v_align,
            'keynote' => $this->keynote,
            'keynoteWidth' => $this->keynote_width,
            'keynoteFont' => $this->keynote_font,
            'keynoteVAlign' => $this->keynote_v_align,
            'backgroundImage' => $this->background_image,
            'buttons' => HeroButtonResource::collection($this->whenLoaded('buttons')),
        ];
    }
}
