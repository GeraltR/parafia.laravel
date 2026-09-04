<?php

namespace App\Services;

use App\Models\InfoItem;
use Illuminate\Http\UploadedFile;

class InfoItemService
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function create(array $data): InfoItem
    {
        return InfoItem::create($this->mapData($data));
    }

    public function update(InfoItem $infoItem, array $data): InfoItem
    {
        $infoItem->update($this->mapData($data));

        return $infoItem;
    }

    public function storeImage(UploadedFile $file, string $baseUrl, ?int $authorId = null): string
    {
        $path = $file->store('info-items', 'public');
        $url = rtrim($baseUrl, '/').'/storage/'.$path;

        $this->mediaService->record($file, $path, $url, $authorId);

        return $url;
    }

    private function mapData(array $data): array
    {
        return [
            'valid_from' => $data['validFrom'],
            'valid_to' => $data['validTo'],
            'title' => $data['title'],
            'short_info' => $data['shortInfo'],
            'description' => $data['description'],
            'image' => $data['image'] ?? null,
            'progress_value' => $data['progressValue'] ?? null,
            'progress_description' => $data['progressDescription'] ?? null,
            'information' => $data['information'] ?? null,
            'banner_text' => $data['bannerText'] ?? null,
            'banner_font' => $data['bannerFont'] ?? null,
            'banner_text_color' => $data['bannerTextColor'] ?? null,
            'banner_bg_color' => $data['bannerBgColor'] ?? null,
            'banner_duration_seconds' => $data['bannerDurationSeconds'],
            'author_id' => $data['authorId'],
        ];
    }
}
