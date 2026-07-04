<?php

namespace Database\Seeders;

use App\Models\Hero;
use Illuminate\Database\Seeder;

class HeroSeeder extends Seeder
{
    public function run(): void
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
            'background_image' => $data['backgroundImage'],
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
}
