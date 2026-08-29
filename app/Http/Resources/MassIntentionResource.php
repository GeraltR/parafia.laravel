<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MassIntentionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->toDateString(),
            'time' => Carbon::parse($this->time)->format('H:i'),
            'intention' => $this->intention,
            'isHoliday' => $this->is_holiday,
            'dayDescription' => $this->day_description,
            'author' => $this->whenLoaded(
                'author',
                fn () => $this->author ? ['id' => $this->author->id, 'name' => $this->author->name] : null
            ),
        ];
    }
}
