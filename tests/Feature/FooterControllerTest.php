<?php

namespace Tests\Feature;

use App\Models\FooterConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_is_public(): void
    {
        FooterConfig::create(['copyright_text' => 'Test copyright']);

        $response = $this->getJson('/api/footer');

        $response->assertOk();
        $response->assertJsonPath('data.copyrightText', 'Test copyright');
    }

    public function test_show_creates_default_row_when_missing(): void
    {
        $this->assertDatabaseCount('footer_configs', 0);

        $response = $this->getJson('/api/footer');

        $response->assertOk();
        $response->assertJsonCount(0, 'data.officeHours');
        $response->assertJsonCount(0, 'data.legalLinks');
        $this->assertDatabaseCount('footer_configs', 1);
    }
}
