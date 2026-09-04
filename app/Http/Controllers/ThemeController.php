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
        'privacy_policy' => null,
        'accessibility_statement' => null,
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
            'privacy_policy' => $data['privacyPolicy'] ?? null,
            'accessibility_statement' => $data['accessibilityStatement'] ?? null,
        ]);

        return ThemeResource::make($theme);
    }
}
