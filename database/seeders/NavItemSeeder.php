<?php

namespace Database\Seeders;

use App\Models\NavItem;
use Illuminate\Database\Seeder;

class NavItemSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/navbar.json')), true);

        foreach ($data['items'] as $order => $item) {
            $navItem = NavItem::create([
                'label' => $item['label'],
                'href' => $item['href'],
                'order' => $order,
                'is_locked' => $item['locked'] ?? false,
            ]);

            foreach ($item['children'] ?? [] as $childOrder => $child) {
                NavItem::create([
                    'parent_id' => $navItem->id,
                    'label' => $child['label'],
                    'href' => $child['href'],
                    'order' => $childOrder,
                ]);
            }
        }
    }
}
