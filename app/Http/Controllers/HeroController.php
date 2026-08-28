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
    private const DEFAULTS = [
        'title_width' => 10,
        'title_v_align' => 'center',
        'subtitle_width' => 8,
        'subtitle_v_align' => 'center',
        'keynote_width' => 10,
        'keynote_v_align' => 'center',
    ];

    public function __construct(private readonly HeroService $heroService) {}

    public function show(): HeroResource
    {
        $hero = Hero::firstOrCreate([], self::DEFAULTS);
        $hero->wasRecentlyCreated = false;
        $hero->load('buttons');

        return HeroResource::make($hero);
    }

    public function update(UpdateHeroRequest $request): HeroResource
    {
        $hero = Hero::firstOrCreate([], self::DEFAULTS);
        $hero->load('buttons');
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
