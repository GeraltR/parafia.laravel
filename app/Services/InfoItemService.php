<?php

namespace App\Services;

use App\Models\InfoItem;
use Illuminate\Http\UploadedFile;

class InfoItemService
{
    public function create(array $data): InfoItem
    {
        return InfoItem::create($this->mapData($data));
    }

    public function update(InfoItem $infoItem, array $data): InfoItem
    {
        $infoItem->update($this->mapData($data));

        return $infoItem;
    }

    public function storeImage(UploadedFile $file, string $baseUrl): string
    {
        $path = $file->store('info-items', 'public');

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }

    private function mapData(array $data): array
    {
        return [
            'valid_from' => $data['validFrom'],
            'valid_to' => $data['validTo'],
            'title' => $data['title'],
            'short_info' => $data['shortInfo'],
            'description' => $data['description'],
            'image' => $data['image'],
            'progress_value' => $data['progressValue'],
            'progress_description' => $data['progressDescription'],
            'information' => $data['information'] ?? null,
            'author_id' => $data['authorId'],
        ];
    }
}
