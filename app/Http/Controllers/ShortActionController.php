<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateShortActionsRequest;
use App\Http\Requests\UploadShortActionIconRequest;
use App\Http\Resources\ShortActionItemResource;
use App\Http\Resources\ShortActionsConfigResource;
use App\Models\ShortActionItem;
use App\Models\ShortActionsConfig;
use App\Services\ShortActionService;
use Illuminate\Http\JsonResponse;

class ShortActionController extends Controller
{
    public function __construct(private readonly ShortActionService $shortActionService) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->payload()]);
    }

    public function update(UpdateShortActionsRequest $request): JsonResponse
    {
        $config = ShortActionsConfig::firstOrFail();
        $this->shortActionService->update($config, $request->validated());

        return response()->json(['data' => $this->payload()]);
    }

    public function uploadIcon(UploadShortActionIconRequest $request): JsonResponse
    {
        $url = $this->shortActionService->storeIcon(
            $request->file('icon'),
            $request->getSchemeAndHttpHost()
        );

        return response()->json(['url' => $url]);
    }

    private function payload(): array
    {
        return [
            'config' => ShortActionsConfigResource::make(ShortActionsConfig::firstOrFail()),
            'items' => ShortActionItemResource::collection(ShortActionItem::orderBy('id')->get()),
        ];
    }
}
