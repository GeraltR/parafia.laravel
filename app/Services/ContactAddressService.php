<?php

namespace App\Services;

use App\Models\ContactAddress;
use App\Models\FooterConfig;
use App\Models\Social;

class ContactAddressService
{
    public function update(array $data): ContactAddress
    {
        $footerConfig = FooterConfig::firstOrCreate();
        $contact = ContactAddress::firstOrCreate([], [
            'footer_config_id' => $footerConfig->id,
            'street' => '',
            'city' => '',
            'post_code' => '',
            'phone' => '',
        ]);

        $contact->update([
            'street' => $data['street'],
            'city' => $data['city'],
            'post_code' => $data['postCode'],
            'phone' => $data['phone'],
            'nip' => $data['nip'] ?? null,
            'bank_account_number' => $data['bankAccountNumber'] ?? null,
            'bank_name' => $data['bankName'] ?? null,
        ]);

        foreach (Social::NETWORKS as $network) {
            Social::firstOrCreate(
                ['footer_config_id' => $contact->footer_config_id, 'social_name' => $network],
                ['social_link' => '', 'visibility' => false]
            )->update([
                'visibility' => $data['social'][$network] ?? false,
            ]);
        }

        return $contact;
    }
}
