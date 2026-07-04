<?php

namespace Database\Seeders;

use App\Models\NavItem;
use Illuminate\Database\Seeder;

class NavItemSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/navbar.json')), true);

        foreach ($data['items'] as $item) {
            NavItem::create([
                'label' => $item['label'],
                'href' => $item['href'],
            ]);
        }
    }
}
