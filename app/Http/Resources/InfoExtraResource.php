<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InfoExtraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'images' => $this->images ?? [],
            'progressPercent' => $this->progress_percent,
            'bankAccount' => $this->bank_account,
            'donationUrl' => $this->donation_url,
            'active' => (bool) $this->active,
        ];
    }
}
