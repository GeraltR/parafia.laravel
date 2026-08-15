<?php

namespace App\Http\Controllers;

use App\Models\Social;
use Illuminate\Http\JsonResponse;

class SocialController extends Controller
{
    public function index(): JsonResponse
    {
        $links = Social::pluck('social_link', 'social_name');

        $data = collect(Social::NETWORKS)
            ->mapWithKeys(fn (string $network) => [$network => $links[$network] ?? ''])
            ->all();

        return response()->json(['data' => $data]);
    }
}
