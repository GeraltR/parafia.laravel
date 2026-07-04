<?php

namespace Database\Seeders;

use App\Models\FooterConfig;
use Illuminate\Database\Seeder;

class FooterConfigSeeder extends Seeder
{
    public function run(): void
    {
        $footer = json_decode(file_get_contents(database_path('seeders/data/footer.json')), true);
        $contact = json_decode(file_get_contents(database_path('seeders/data/contactAddresses.json')), true);
        $social = json_decode(file_get_contents(database_path('seeders/data/social.json')), true);

        $footerConfig = FooterConfig::create([
            'office_note' => $footer['officeNote'],
            'map_embed_url' => $footer['mapEmbedUrl'],
            'map_link' => $footer['mapLink'],
            'copyright_text' => $footer['copyrightText'],
        ]);

        foreach ($footer['officeHours'] as $officeHour) {
            preg_match('/^(\d{1,2}:\d{2})\s*[-–—]\s*(\d{1,2}:\d{2})$/u', $officeHour['hours'], $hoursMatch);

            $footerConfig->officeHours()->create([
                'day' => $officeHour['day'],
                'hours_on' => $hoursMatch[1],
                'hours_end' => $hoursMatch[2],
            ]);
        }

        foreach ($footer['legalLinks'] as $legalLink) {
            $footerConfig->legalLinks()->create([
                'label' => $legalLink['label'],
                'href' => $legalLink['href'],
            ]);
        }

        $visibility = $contact['social'];

        [$street, $rest] = array_map('trim', explode(',', $contact['address'], 2));
        preg_match('/^(\d{2}-\d{3})\s+(.+)$/u', $rest, $addressMatch);

        $footerConfig->contactAddresses()->create([
            'street' => $street,
            'post_code' => $addressMatch[1],
            'city' => $addressMatch[2],
            'phone' => $contact['phone'],
            'social' => in_array(true, $visibility, true),
        ]);

        foreach ($social as $platform => $link) {
            $footerConfig->social()->create([
                'social_name' => $platform,
                'social_link' => $link,
                'visibility' => $visibility[$platform] ?? false,
            ]);
        }
    }
}
