<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FontService
{
    public function list(string $baseUrl): array
    {
        $families = [];

        foreach (Storage::disk('public')->files('fonts') as $path) {
            if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'woff2') {
                continue;
            }

            [$family, $weight, $style] = $this->parseFilename(pathinfo($path, PATHINFO_FILENAME));

            $families[$family] ??= ['family' => $family, 'variants' => []];
            $families[$family]['variants'][] = [
                'url' => rtrim($baseUrl, '/').'/storage/'.$path,
                'weight' => $weight,
                'style' => $style,
            ];
        }

        ksort($families);

        return array_values($families);
    }

    /**
     * Expects file names like "FamilyName-400.woff2" or "FamilyName-700italic.woff2".
     * Falls back to treating the whole file name as the family (weight 400, normal style).
     *
     * @return array{0: string, 1: int, 2: string}
     */
    private function parseFilename(string $filename): array
    {
        if (preg_match('/^(.+)-(\d{3})(italic)?$/i', $filename, $matches)) {
            return [$matches[1], (int) $matches[2], ($matches[3] ?? '') !== '' ? 'italic' : 'normal'];
        }

        return [$filename, 400, 'normal'];
    }
}
