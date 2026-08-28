<?php

namespace Database\Seeders;

use App\Models\MassAndPastorSection;
use Illuminate\Database\Seeder;

class MassAndPastorSeeder extends Seeder
{
    public function run(): void
    {
        $section = MassAndPastorSection::create([]);

        $section->massTimes()->createMany([
            ['label' => 'Niedziela i święta', 'hours' => '7:30, 9:30, 11:30, 18:00', 'order' => 0],
            ['label' => 'Dni powszednie', 'hours' => '6:30, 18:00', 'order' => 1],
            [
                'label' => 'Spowiedź',
                'hours' => 'codziennie',
                'note' => 'W trakcie Mszy Świętej lub po niej, na prośbę w zakrystii.',
                'order' => 2,
            ],
        ]);

        $section->pastors()->createMany([
            [
                'position' => 'Proboszcz',
                'full_name' => 'ks. Józef Gut',
                'duties' => '<p>Ruch Spotkań Małżeńskich, Parafialne Koło „Caritas”.</p>',
                'order' => 0,
            ],
            [
                'position' => 'Wikariusz',
                'full_name' => 'ks. Łukasz Rzemiński',
                'duties' => '<p>Ministranci, lektorzy, Koło Radia Maryja, Straż Honorowa NSPJ.</p>',
                'order' => 1,
            ],
        ]);
    }
}
