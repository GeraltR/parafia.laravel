<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateContactAddressRequest;
use App\Models\ContactAddress;
use App\Models\FooterConfig;
use App\Models\Social;
use App\Services\ContactAddressService;
use Illuminate\Http\JsonResponse;

class ContactAddressController extends Controller
{
    public function __construct(private readonly ContactAddressService $contactAddressService) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->payload()]);
    }

    public function update(UpdateContactAddressRequest $request): JsonResponse
    {
        $this->contactAddressService->update($request->validated());

        return response()->json(['data' => $this->payload()]);
    }

    private function payload(): array
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

        return [
            'id' => $contact->id,
            'address' => "{$contact->street}, {$contact->post_code} {$contact->city}",
            'street' => $contact->street,
            'city' => $contact->city,
            'postCode' => $contact->post_code,
            'phone' => $contact->phone,
            'social' => $visibility,
        ];
    }
}
