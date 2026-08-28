<?php

namespace Database\Seeders;

use App\Models\AssociationsConfig;
use Illuminate\Database\Seeder;

class AssociationSeeder extends Seeder
{
    public function run(): void
    {
        $config = AssociationsConfig::create([]);

        $names = [
            'Ministranci',
            'Parafialne Koło „Caritas”',
            'Koło Radia Maryja',
            'Straż Honorowa Najświętszego Serca Pana Jezusa',
            'Żywy Różaniec',
            'Ruch Spotkań Małżeńskich',
        ];

        foreach ($names as $order => $name) {
            $config->associations()->create([
                'name' => $name,
                'link' => '#',
                'order' => $order,
            ]);
        }
    }
}
