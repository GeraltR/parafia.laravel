<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FontControllerTest extends TestCase
{
    public function test_index_groups_variants_by_family(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('fonts/Geist-400.woff2', 'fake-font-data');
        Storage::disk('public')->put('fonts/Geist-700italic.woff2', 'fake-font-data');
        Storage::disk('public')->put('fonts/Merriweather.woff2', 'fake-font-data');
        Storage::disk('public')->put('fonts/readme.txt', 'not a font');

        $response = $this->getJson('/api/fonts');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson([
            [
                'family' => 'Geist',
                'variants' => [
                    ['weight' => 400, 'style' => 'normal'],
                    ['weight' => 700, 'style' => 'italic'],
                ],
            ],
            [
                'family' => 'Merriweather',
                'variants' => [
                    ['weight' => 400, 'style' => 'normal'],
                ],
            ],
        ]);
    }

    public function test_index_returns_empty_array_when_no_fonts_uploaded(): void
    {
        Storage::fake('public');

        $response = $this->getJson('/api/fonts');

        $response->assertOk();
        $response->assertExactJson([]);
    }
}
