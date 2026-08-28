<?php

namespace Tests\Feature;

use App\Models\ContactAddress;
use App\Models\FooterConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactAddressControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_is_public(): void
    {
        $footer = FooterConfig::create([]);
        ContactAddress::create([
            'footer_config_id' => $footer->id,
            'street' => 'ul. Testowa 1',
            'city' => 'Testowo',
            'post_code' => '00-000',
            'phone' => '+48 111 222 333',
        ]);

        $response = $this->getJson('/api/contact-addresses');

        $response->assertOk();
        $response->assertJsonPath('data.address', 'ul. Testowa 1, 00-000 Testowo');
        $response->assertJsonPath('data.phone', '+48 111 222 333');
    }

    public function test_show_creates_default_rows_when_missing(): void
    {
        $this->assertDatabaseCount('footer_configs', 0);
        $this->assertDatabaseCount('contact_addresses', 0);

        $response = $this->getJson('/api/contact-addresses');

        $response->assertOk();
        $this->assertDatabaseCount('footer_configs', 1);
        $this->assertDatabaseCount('contact_addresses', 1);
    }
}
