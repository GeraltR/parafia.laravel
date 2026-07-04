<?php

namespace Database\Seeders;

use App\Models\InfoExtra;
use Illuminate\Database\Seeder;

class InfoExtraSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/infoExtra.json')), true);

        InfoExtra::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'images' => $data['images'],
            'progress_percent' => $data['progressPercent'],
            'bank_account' => $data['bankAccount'],
            'donation_url' => $data['donationUrl'],
            'active' => $data['active'] ?? true,
        ]);
    }
}
