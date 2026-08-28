<?php

namespace App\Services;

use App\Models\FooterConfig;
use App\Models\OfficeHour;

class FooterService
{
    public function update(FooterConfig $footer, array $data): void
    {
        $footer->update([
            'office_title' => $data['officeTitle'],
            'office_note' => $data['officeNote'] ?? '',
            'map_embed_url' => $data['mapEmbedUrl'] ?? '',
            'map_link' => $data['mapLink'] ?? '',
            'bg_color' => $data['config']['bgColor'] ?? null,
            'title_font' => $data['config']['titleFont'] ?? null,
            'title_size' => $data['config']['titleSize'] ?? null,
            'title_color' => $data['config']['titleColor'] ?? null,
        ]);

        $this->syncOfficeHours($footer, $data['officeHours'] ?? []);
    }

    private function syncOfficeHours(FooterConfig $footer, array $officeHours): void
    {
        $keepIds = [];

        foreach ($officeHours as $officeHour) {
            $attributes = [
                'day' => $officeHour['day'],
                'hours_on' => $officeHour['hoursOn'],
                'hours_end' => $officeHour['hoursEnd'],
            ];

            $existing = ! empty($officeHour['id']) ? $footer->officeHours()->find($officeHour['id']) : null;

            if ($existing) {
                $existing->update($attributes);
                $keepIds[] = $existing->id;

                continue;
            }

            $keepIds[] = $footer->officeHours()->create($attributes)->id;
        }

        OfficeHour::where('footer_config_id', $footer->id)->whereNotIn('id', $keepIds)->delete();
    }
}
