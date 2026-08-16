<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateHeroRequest;
use App\Http\Requests\UploadHeroBackgroundImageRequest;
use App\Http\Resources\HeroResource;
use App\Models\Hero;
use App\Services\HeroService;
use Illuminate\Http\JsonResponse;

class HeroController extends Controller
{
    public function __construct(private readonly HeroService $heroService) {}

    public function show(): HeroResource
    {
        return HeroResource::make(Hero::with('buttons')->firstOrFail());
    }

    public function update(UpdateHeroRequest $request): HeroResource
    {
        $hero = Hero::with('buttons')->firstOrFail();
        $hero = $this->heroService->update($hero, $request->validated());

        return HeroResource::make($hero);
    }

    public function uploadBackgroundImage(UploadHeroBackgroundImageRequest $request): JsonResponse
    {
        $url = $this->heroService->storeBackgroundImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost()
        );

        return response()->json(['url' => $url]);
    }
}
