<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FooterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'officeTitle' => $this->office_title,
            'officeHours' => OfficeHourResource::collection($this->whenLoaded('officeHours')),
            'officeNote' => $this->office_note,
            'mapEmbedUrl' => $this->map_embed_url,
            'mapLink' => $this->map_link,
            'legalLinks' => FooterLegalLinkResource::collection($this->whenLoaded('legalLinks')),
            'copyrightText' => $this->copyright_text,
            'config' => [
                'bgColor' => $this->bg_color,
                'titleFont' => $this->title_font,
                'titleSize' => $this->title_size,
                'titleColor' => $this->title_color,
            ],
        ];
    }
}
