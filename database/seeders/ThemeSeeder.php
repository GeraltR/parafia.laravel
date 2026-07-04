<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/theme.json')), true);

        Theme::create([
            'primary_color' => $data['primaryColor'],
            'secondary_color' => $data['secondaryColor'],
            'font_heading' => $data['fontHeading'],
            'font_body' => $data['fontBody'],
            'title' => $data['title'],
            'subtitle' => $data['subtitle'],
        ]);
    }
}
