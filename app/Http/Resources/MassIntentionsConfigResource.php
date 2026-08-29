<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MassIntentionsConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'holidayDescribedColor' => $this->holiday_described_color,
            'holidayPlainColor' => $this->holiday_plain_color,
            'weekdayColor' => $this->weekday_color,
        ];
    }
}
