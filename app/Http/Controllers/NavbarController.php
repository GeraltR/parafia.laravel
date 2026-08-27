<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNavbarRequest;
use App\Http\Resources\NavItemResource;
use App\Models\NavItem;
use App\Services\NavbarService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class NavbarController extends Controller
{
    public function __construct(private readonly NavbarService $navbarService) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'data' => [
                'items' => NavItemResource::collection($this->topLevelItems()),
            ],
        ]);
    }

    public function update(UpdateNavbarRequest $request): JsonResponse
    {
        $this->navbarService->sync($request->validated('items', []));

        return response()->json([
            'data' => [
                'items' => NavItemResource::collection($this->topLevelItems()),
            ],
        ]);
    }

    private function topLevelItems(): Collection
    {
        return NavItem::whereNull('parent_id')->orderBy('order')->with('children')->get();
    }
}
