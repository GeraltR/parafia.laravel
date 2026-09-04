<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThemeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'primaryColor' => $this->primary_color,
            'secondaryColor' => $this->secondary_color,
            'fontHeading' => $this->font_heading,
            'fontBody' => $this->font_body,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'privacyPolicy' => $this->privacy_policy,
            'accessibilityStatement' => $this->accessibility_statement,
        ];
    }
}
