<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSocialRequest;
use App\Models\Social;
use App\Services\SocialService;
use Illuminate\Http\JsonResponse;

class SocialController extends Controller
{
    public function __construct(private readonly SocialService $socialService) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->payload()]);
    }

    public function update(UpdateSocialRequest $request): JsonResponse
    {
        $this->socialService->update($request->validated());

        return response()->json(['data' => $this->payload()]);
    }

    private function payload(): array
    {
        $links = Social::pluck('social_link', 'social_name');

        return collect(Social::NETWORKS)
            ->mapWithKeys(fn (string $network) => [$network => $links[$network] ?? ''])
            ->all();
    }
}
