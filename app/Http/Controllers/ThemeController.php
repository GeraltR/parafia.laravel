<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateThemeRequest;
use App\Http\Resources\ThemeResource;
use App\Models\Theme;

class ThemeController extends Controller
{
    private const DEFAULTS = [
        'primary_color' => '#1a365d',
        'secondary_color' => '#c9a84c',
        'font_heading' => 'Merriweather',
        'font_body' => 'Inter',
        'title' => '',
        'subtitle' => '',
    ];

    public function show(): ThemeResource
    {
        $theme = Theme::firstOrCreate([], self::DEFAULTS);
        $theme->wasRecentlyCreated = false;

        return ThemeResource::make($theme);
    }

    public function update(UpdateThemeRequest $request): ThemeResource
    {
        $data = $request->validated();

        $theme = Theme::firstOrCreate([], self::DEFAULTS);
        $theme->wasRecentlyCreated = false;
        $theme->update([
            'primary_color' => $data['primaryColor'],
            'secondary_color' => $data['secondaryColor'],
            'font_heading' => $data['fontHeading'],
            'font_body' => $data['fontBody'],
            'title' => $data['title'],
            'subtitle' => $data['subtitle'],
        ]);

        return ThemeResource::make($theme);
    }
}
