<?php

namespace App\Http\Controllers;

use App\Models\ContactAddress;
use App\Models\Social;
use Illuminate\Http\JsonResponse;

class ContactAddressController extends Controller
{
    public function show(): JsonResponse
    {
        $contact = ContactAddress::firstOrFail();

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
