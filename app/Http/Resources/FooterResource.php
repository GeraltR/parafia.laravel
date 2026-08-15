<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FooterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'officeHours' => OfficeHourResource::collection($this->whenLoaded('officeHours')),
            'officeNote' => $this->office_note,
            'mapEmbedUrl' => $this->map_embed_url,
            'mapLink' => $this->map_link,
            'legalLinks' => FooterLegalLinkResource::collection($this->whenLoaded('legalLinks')),
            'copyrightText' => $this->copyright_text,
        ];
    }
}
