<?php

namespace App\Services;

use App\Models\Association;
use App\Models\AssociationsConfig;
use Illuminate\Http\UploadedFile;

class AssociationService
{
    public function update(AssociationsConfig $config, array $data): void
    {
        $config->update([
            'name_font' => $data['config']['nameFont'] ?? null,
            'name_size' => $data['config']['nameSize'] ?? null,
        ]);

        $keepIds = [];

        foreach ($data['items'] ?? [] as $index => $item) {
            $attributes = [
                'name' => $item['name'],
                'image_url' => $item['imageUrl'] ?? null,
                'link' => $item['link'],
                'order' => $index,
            ];

            $existing = ! empty($item['id']) ? $config->associations()->find($item['id']) : null;

            if ($existing) {
                $existing->update($attributes);
                $keepIds[] = $existing->id;

                continue;
            }

            $keepIds[] = $config->associations()->create($attributes)->id;
        }

        Association::where('associations_config_id', $config->id)->whereNotIn('id', $keepIds)->delete();
    }

    public function storeImage(UploadedFile $file, string $baseUrl): string
    {
        $path = $file->store('associations', 'public');

        return rtrim($baseUrl, '/').'/storage/'.$path;
    }
}
