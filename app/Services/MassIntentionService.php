<?php

namespace App\Services;

use App\Models\MassIntention;
use App\Models\MassIntentionsConfig;

class MassIntentionService
{
    public function create(array $data): MassIntention
    {
        $intention = MassIntention::create([
            'date' => $data['date'],
            'time' => $data['time'],
            'intention' => $data['intention'],
            'is_holiday' => $data['isHoliday'] ?? false,
            'day_description' => $data['dayDescription'] ?? null,
            'author_id' => $data['authorId'] ?? null,
        ]);

        $this->syncDay($intention);

        return $intention;
    }

    public function update(MassIntention $intention, array $data): MassIntention
    {
        $intention->update([
            'date' => $data['date'],
            'time' => $data['time'],
            'intention' => $data['intention'],
            'is_holiday' => $data['isHoliday'] ?? false,
            'day_description' => $data['dayDescription'] ?? null,
            'author_id' => $data['authorId'] ?? null,
        ]);

        $this->syncDay($intention);

        return $intention;
    }

    public function updateConfig(array $data): MassIntentionsConfig
    {
        $config = MassIntentionsConfig::firstOrCreate();
        $config->wasRecentlyCreated = false;
        $config->update([
            'holiday_described_color' => $data['holidayDescribedColor'],
            'holiday_plain_color' => $data['holidayPlainColor'],
            'weekday_color' => $data['weekdayColor'],
        ]);

        return $config;
    }

    /**
     * The parish office fills the "is this a holiday / feast day" flag and its
     * description once per day, on whichever intention they happen to be
     * editing — every other mass on that same day must show the same flag and
     * description, so we propagate it across the whole day group here
     * (mirrors the previous WordPress plugin's behavior).
     */
    private function syncDay(MassIntention $intention): void
    {
        MassIntention::where('date', $intention->date)
            ->where('id', '!=', $intention->id)
            ->update([
                'is_holiday' => $intention->is_holiday,
                'day_description' => $intention->day_description,
            ]);
    }
}
