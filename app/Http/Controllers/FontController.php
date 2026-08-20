<?php

namespace App\Http\Controllers;

use App\Services\FontService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FontController extends Controller
{
    public function __construct(private readonly FontService $fontService) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->fontService->list($request->getSchemeAndHttpHost())
        );
    }
}
