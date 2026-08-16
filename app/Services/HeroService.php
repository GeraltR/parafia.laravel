<?php

namespace App\Services;

use App\Models\Hero;
use Illuminate\Http\UploadedFile;

class HeroService
{
    public function update(Hero $hero, array $data): Hero
    {
        $hero->update([
            'title' => $data['title'],
            'title_width' => $data['titleWidth'],
            'title_font' => $data['titleFont'] ?? '',
            'title_v_align' => $data['titleVAlign'],
            'subtitle' => $data['subtitle'] ?? '',
            'subtitle_width' => $data['subtitleWidth'],
            'subtitle_font' => $data['subtitleFont'] ?? '',
            'subtitle_v_align' => $data['subtitleVAlign'],
            'keynote' => $data['keynote'] ?? '',
            'keynote_width' => $data['keynoteWidth'],
            'keynote_font' => $data['keynoteFont'] ?? '',
            'keynote_v_align' => $data['keynoteVAlign'],
            'background_image' => $data['backgroundImage'],
        ]);

        $this->syncButtons($hero, $data['buttons'] ?? []);

        return $hero->fresh('buttons');
    }

    private function syncButtons(Hero $hero, array $buttons): void
    {
        $keepIds = [];

        foreach ($buttons as $button) {
            $attributes = [
                'label' => $button['label'],
                'href' => $button['href'],
                'icon' => $button['icon'],
                'external' => $button['external'] ?? false,
            ];

            $existing = ! empty($button['id'])
                ? $hero->buttons()->find($button['id'])
                : null;

            if ($existing) {
                $existing->update($attributes);
                $keepIds[] = $existing->id;

                continue;
            }

            $created = $hero->buttons()->create($attributes);
            $keepIds[] = $created->id;
        }

        $hero->buttons()->whereNotIn('id', $keepIds)->delete();
    }

    public function storeBackgroundImage(UploadedFile $file, string $baseUrl): string
    {
        $path = $file->store('hero', 'public');

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }
}
