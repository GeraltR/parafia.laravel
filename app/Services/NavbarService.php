<?php

namespace App\Services;

use App\Models\NavItem;

class NavbarService
{
    public function sync(array $items): void
    {
        $keepIds = [];

        foreach ($items as $index => $item) {
            $navItem = $this->syncItem($item, null, $index);
            $keepIds[] = $navItem->id;

            $childKeepIds = $navItem->children()->where('is_locked', true)->pluck('id')->all();
            foreach ($item['children'] ?? [] as $childIndex => $child) {
                $childItem = $this->syncItem($child, $navItem->id, $childIndex);
                $childKeepIds[] = $childItem->id;
            }

            $navItem->children()->whereNotIn('id', array_unique($childKeepIds))->delete();
        }

        $lockedTopLevelIds = NavItem::whereNull('parent_id')->where('is_locked', true)->pluck('id')->all();

        NavItem::whereNull('parent_id')
            ->whereNotIn('id', array_unique([...$keepIds, ...$lockedTopLevelIds]))
            ->delete();
    }

    private function syncItem(array $item, ?int $parentId, int $order): NavItem
    {
        $attributes = [
            'parent_id' => $parentId,
            'label' => $item['label'],
            'href' => $item['href'],
            'order' => $order,
        ];

        $existing = ! empty($item['id']) ? NavItem::find($item['id']) : null;

        if ($existing) {
            $existing->update($attributes);

            return $existing;
        }

        return NavItem::create($attributes);
    }
}
