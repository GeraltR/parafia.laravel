<?php

namespace App\Http\Controllers;

use App\Http\Resources\NavItemResource;
use App\Models\NavItem;
use Illuminate\Http\JsonResponse;

class NavbarController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json([
            'data' => [
                'items' => NavItemResource::collection(NavItem::all()),
            ],
        ]);
    }
}
