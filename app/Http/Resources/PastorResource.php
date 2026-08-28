<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PastorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'fullName' => $this->full_name,
            'photoUrl' => $this->photo_url,
            'duties' => $this->duties,
            'order' => $this->order,
        ];
    }
}
