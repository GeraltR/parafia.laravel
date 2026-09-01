<?php

namespace Database\Seeders;

use App\Models\SakramentyTopic;
use App\Models\User;
use Illuminate\Database\Seeder;

class SakramentyTopicSeeder extends Seeder
{
    public function run(): void
    {
        if (SakramentyTopic::query()->exists()) {
            return;
        }

        $author = User::where('email', 'test@example.com')->first() ?? User::first();

        $sakramenty = ['Chrzest', 'Bierzmowanie', 'Małżeństwo'];

        foreach ($sakramenty as $order => $title) {
            SakramentyTopic::create([
                'title' => $title,
                'content' => '',
                'author_id' => $author?->id,
                'order' => $order,
            ]);
        }
    }
}
