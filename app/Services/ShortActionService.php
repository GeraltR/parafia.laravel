<?php

namespace App\Services;

use App\Models\ShortActionItem;
use App\Models\ShortActionsConfig;
use Illuminate\Http\UploadedFile;

class ShortActionService
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function update(ShortActionsConfig $config, array $data): void
    {
        $config->update([
            'title_font' => $data['config']['titleFont'] ?? null,
            'title_size' => $data['config']['titleSize'] ?? null,
            'title_color' => $data['config']['titleColor'] ?? null,
            'subtitle_font' => $data['config']['subtitleFont'] ?? null,
            'subtitle_size' => $data['config']['subtitleSize'] ?? null,
            'subtitle_color' => $data['config']['subtitleColor'] ?? null,
            'bg_color' => $data['config']['bgColor'] ?? null,
            'bg_color_hover' => $data['config']['bgColorHover'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            ShortActionItem::whereKey($item['id'])->update([
                'icon' => $item['icon'] ?? null,
                'icon_url' => $item['iconUrl'] ?? null,
                'title' => $item['title'],
                'description' => $item['description'],
                'href' => $item['href'],
                'external' => $item['external'] ?? false,
            ]);
        }
    }

    public function storeIcon(UploadedFile $file, string $baseUrl, ?int $authorId = null): string
    {
        $path = $file->store('short-actions', 'public');
        $url = rtrim($baseUrl, '/').'/storage/'.$path;

        $this->mediaService->record($file, $path, $url, $authorId);

        return $url;
    }
}
