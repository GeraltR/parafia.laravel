<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroButtonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'href' => $this->href,
            'icon' => $this->icon,
            'external' => (bool) $this->external,
            'textColor' => $this->text_color,
            'textColorHover' => $this->text_color_hover,
            'bgColor' => $this->bg_color,
            'bgColorHover' => $this->bg_color_hover,
        ];
    }
}
