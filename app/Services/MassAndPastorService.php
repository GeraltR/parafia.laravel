<?php

namespace App\Services;

use App\Models\MassAndPastorSection;
use App\Models\MassTime;
use App\Models\Pastor;
use Illuminate\Http\UploadedFile;

class MassAndPastorService
{
    public function update(MassAndPastorSection $section, array $data): void
    {
        $section->update([
            'position_font' => $data['config']['positionFont'] ?? null,
            'position_size' => $data['config']['positionSize'] ?? null,
            'position_color' => $data['config']['positionColor'] ?? null,
            'name_font' => $data['config']['nameFont'] ?? null,
            'name_size' => $data['config']['nameSize'] ?? null,
            'name_color' => $data['config']['nameColor'] ?? null,
        ]);

        $this->syncMassTimes($section, $data['massTimes'] ?? []);
        $this->syncPastors($section, $data['pastors'] ?? []);
    }

    private function syncMassTimes(MassAndPastorSection $section, array $massTimes): void
    {
        $keepIds = [];

        foreach ($massTimes as $index => $massTime) {
            $attributes = [
                'label' => $massTime['label'],
                'hours' => $massTime['hours'],
                'note' => $massTime['note'] ?? null,
                'order' => $index,
            ];

            $existing = ! empty($massTime['id']) ? $section->massTimes()->find($massTime['id']) : null;

            if ($existing) {
                $existing->update($attributes);
                $keepIds[] = $existing->id;

                continue;
            }

            $keepIds[] = $section->massTimes()->create($attributes)->id;
        }

        MassTime::where('mass_and_pastor_section_id', $section->id)->whereNotIn('id', $keepIds)->delete();
    }

    private function syncPastors(MassAndPastorSection $section, array $pastors): void
    {
        $keepIds = [];

        foreach ($pastors as $index => $pastor) {
            $attributes = [
                'position' => $pastor['position'],
                'full_name' => $pastor['fullName'],
                'photo_url' => $pastor['photoUrl'] ?? null,
                'duties' => $pastor['duties'] ?? '',
                'order' => $index,
            ];

            $existing = ! empty($pastor['id']) ? $section->pastors()->find($pastor['id']) : null;

            if ($existing) {
                $existing->update($attributes);
                $keepIds[] = $existing->id;

                continue;
            }

            $keepIds[] = $section->pastors()->create($attributes)->id;
        }

        Pastor::where('mass_and_pastor_section_id', $section->id)->whereNotIn('id', $keepIds)->delete();
    }

    public function storePhoto(UploadedFile $file, string $baseUrl): string
    {
        $path = $file->store('pastors', 'public');

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }
}
