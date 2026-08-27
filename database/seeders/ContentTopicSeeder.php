<?php

namespace Database\Seeders;

use App\Models\ContentTopic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentTopicSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'test@example.com')->first() ?? User::first();

        $sakramenty = ['Chrzest', 'Bierzmowanie', 'Małżeństwo'];

        foreach ($sakramenty as $order => $title) {
            ContentTopic::create([
                'page' => 'sakramenty',
                'title' => $title,
                'content' => '',
                'author_id' => $author?->id,
                'order' => $order,
            ]);
        }
    }
}
