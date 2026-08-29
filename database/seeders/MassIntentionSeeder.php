<?php

namespace Database\Seeders;

use App\Models\MassIntention;
use App\Models\MassIntentionsConfig;
use App\Models\User;
use Illuminate\Database\Seeder;

class MassIntentionSeeder extends Seeder
{
    public function run(): void
    {
        MassIntentionsConfig::firstOrCreate();

        if (MassIntention::query()->exists()) {
            return;
        }

        $author = User::where('email', 'test@example.com')->first() ?? User::first();

        $intentions = [
            'Za zmarłych z rodziny Kowalskich',
            'W intencji parafian',
            'Za żywych i zmarłych z rodziny Nowak',
            'W intencji chorych naszej parafii',
            'Za dusze w czyśćcu cierpiące',
            'W podziękowaniu za otrzymane łaski z prośbą o dalsze błogosławieństwo',
            'Za zmarłego męża i rodziców z obu stron',
            'W intencji dzieci i młodzieży',
            'Za zmarłych kapłanów pracujących w tej parafii',
            'W intencji Ojczyzny',
            'Za zmarłą matkę w rocznicę śmierci',
            'W intencji małżonków obchodzących rocznicę ślubu',
            'Za zmarłych z rodziny Wiśniewskich',
            'W intencji powołań kapłańskich i zakonnych',
        ];

        $times = ['07:00', '18:00'];

        for ($day = 0; $day < 14; $day++) {
            $date = now()->addDays($day);
            $isSunday = $date->isSunday();

            MassIntention::create([
                'date' => $date->toDateString(),
                'time' => $times[$day % 2],
                'intention' => $intentions[$day % count($intentions)],
                'is_holiday' => $isSunday,
                'day_description' => $isSunday ? 'Niedziela zwykła' : null,
                'author_id' => $author?->id,
            ]);
        }
    }
}
