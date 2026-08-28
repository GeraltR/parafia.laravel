<?php

namespace App\Http\Controllers;

use App\Models\ContactAddress;
use App\Models\FooterConfig;
use App\Models\Social;
use Illuminate\Http\JsonResponse;

class ContactAddressController extends Controller
{
    public function show(): JsonResponse
    {
        $footerConfig = FooterConfig::firstOrCreate();
        $contact = ContactAddress::firstOrCreate([], [
            'footer_config_id' => $footerConfig->id,
            'street' => '',
            'city' => '',
            'post_code' => '',
            'phone' => '',
        ]);

        $visibilityByNetwork = Social::where('footer_config_id', $contact->footer_config_id)
            ->pluck('visibility', 'social_name');

        $visibility = collect(Social::NETWORKS)
            ->mapWithKeys(fn (string $network) => [$network => (bool) ($visibilityByNetwork[$network] ?? false)])
            ->all();

        return response()->json([
            'data' => [
                'id' => $contact->id,
                'address' => "{$contact->street}, {$contact->post_code} {$contact->city}",
                'phone' => $contact->phone,
                'social' => $visibility,
            ],
        ]);
    }
}
