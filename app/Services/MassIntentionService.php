<?php

namespace App\Services;

use App\Models\MassIntention;
use App\Models\MassIntentionsConfig;
use Illuminate\Database\Eloquent\Builder;

class MassIntentionService
{
    public function manageQuery(?string $search): Builder
    {
        $query = MassIntention::with('author')->orderByDesc('date')->orderBy('time');

        $search = trim((string) $search);

        if ($search !== '') {
            $this->applySearch($query, $search);
        }

        return $query;
    }

    /**
     * Matches the search term against the intention text and day description,
     * plus a loose date match (day/month, optionally year) so admins can find
     * a day by typing "29.08" or "29.8" without worrying about leading zeros.
     */
    private function applySearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $q) use ($search) {
            $q->where('intention', 'like', "%{$search}%")
                ->orWhere('day_description', 'like', "%{$search}%");

            if (preg_match('/^(\d{1,2})[.\/](\d{1,2})(?:[.\/](\d{4}))?$/', $search, $matches)) {
                $day = (int) $matches[1];
                $month = (int) $matches[2];

                $q->orWhere(function (Builder $dateQuery) use ($day, $month, $matches) {
                    $dateQuery->whereDay('date', $day)->whereMonth('date', $month);

                    if (isset($matches[3])) {
                        $dateQuery->whereYear('date', (int) $matches[3]);
                    }
                });
            } elseif (ctype_digit($search) && strlen($search) <= 2) {
                $q->orWhereDay('date', (int) $search);
            }
        });
    }

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
