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
    private const DEFAULT_ICONS = ['mass', 'sacraments', 'announcements', 'office', 'media', 'contact'];

    public function __construct(private readonly ShortActionService $shortActionService) {}

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->payload()]);
    }

    public function update(UpdateShortActionsRequest $request): JsonResponse
    {
        $config = ShortActionsConfig::firstOrCreate();
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
        $this->ensureSixItems();

        return [
            'config' => ShortActionsConfigResource::make(ShortActionsConfig::firstOrCreate()),
            'items' => ShortActionItemResource::collection(ShortActionItem::orderBy('id')->get()),
        ];
    }

    private function ensureSixItems(): void
    {
        $existing = ShortActionItem::count();

        for ($i = $existing; $i < 6; $i++) {
            ShortActionItem::create([
                'icon' => self::DEFAULT_ICONS[$i] ?? 'mass',
                'title' => '',
                'description' => '',
                'href' => '/',
            ]);
        }
    }
}
