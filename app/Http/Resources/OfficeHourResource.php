<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeHourResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'hours' => sprintf(
                '%s – %s',
                Carbon::parse($this->hours_on)->format('H:i'),
                Carbon::parse($this->hours_end)->format('H:i'),
            ),
            'hoursOn' => Carbon::parse($this->hours_on)->format('H:i'),
            'hoursEnd' => Carbon::parse($this->hours_end)->format('H:i'),
        ];
    }
}
