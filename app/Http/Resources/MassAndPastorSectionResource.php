<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MassAndPastorSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'positionFont' => $this->position_font,
            'positionSize' => $this->position_size,
            'positionColor' => $this->position_color,
            'nameFont' => $this->name_font,
            'nameSize' => $this->name_size,
            'nameColor' => $this->name_color,
        ];
    }
}
