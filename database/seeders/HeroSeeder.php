<?php

namespace Database\Seeders;

use App\Models\Hero;
use App\Services\HeroService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class HeroSeeder extends Seeder
{
    public function run(HeroService $heroService): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/hero.json')), true);

        $hero = Hero::create([
            'title' => $data['title'],
            'title_width' => $data['titleWidth'],
            'title_font' => $data['titleFont'],
            'title_v_align' => $data['titleVAlign'],
            'subtitle' => $data['subtitle'],
            'subtitle_width' => $data['subtitleWidth'],
            'subtitle_font' => $data['subtitleFont'],
            'subtitle_v_align' => $data['subtitleVAlign'],
            'keynote' => $data['keynote'],
            'keynote_width' => $data['keynoteWidth'],
            'keynote_font' => $data['keynoteFont'],
            'keynote_v_align' => $data['keynoteVAlign'],
            'background_image' => $this->seedBackgroundImage($heroService),
        ]);

        foreach ($data['buttons'] as $button) {
            $hero->buttons()->create([
                'label' => $button['label'],
                'href' => $button['href'],
                'icon' => $button['icon'],
                'external' => $button['external'] ?? false,
            ]);
        }
    }

    /**
     * Stores the seed background image through the same HeroService::storeBackgroundImage()
     * path a real admin-panel upload uses, so the seeded Hero behaves identically to one
     * created via the app (same disk, same generated file name, same URL shape).
     */
    private function seedBackgroundImage(HeroService $heroService): string
    {
        $sourcePath = database_path('seeders/data/sklepienie_swieci.png');
        $file = new UploadedFile($sourcePath, 'sklepienie_swieci.png', null, null, true);

        return $heroService->storeBackgroundImage($file, config('app.url'));
    }
}
