<?php

namespace App\Services;

use App\Models\FooterConfig;
use App\Models\Social;

class SocialService
{
    public function update(array $links): void
    {
        $footerConfig = FooterConfig::firstOrCreate();

        foreach (Social::NETWORKS as $network) {
            Social::firstOrCreate(
                ['footer_config_id' => $footerConfig->id, 'social_name' => $network],
                ['social_link' => '', 'visibility' => false]
            )->update([
                'social_link' => $links[$network] ?? '',
            ]);
        }
    }
}
