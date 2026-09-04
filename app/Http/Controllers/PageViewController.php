<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageViewRequest;
use App\Services\PageViewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageViewController extends Controller
{
    public function __construct(private readonly PageViewService $pageViewService)
    {
    }

    public function store(StorePageViewRequest $request): JsonResponse
    {
        $this->pageViewService->record(
            $request->validated('path'),
            $request->validated('referrer'),
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json(null, 204);
    }

    public function summary(Request $request): JsonResponse
    {
        $days = max(1, min(365, (int) $request->query('days', 30)));

        return response()->json($this->pageViewService->summary($days));
    }
}
