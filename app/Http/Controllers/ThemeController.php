<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateThemeRequest;
use App\Http\Resources\ThemeResource;
use App\Models\Theme;

class ThemeController extends Controller
{
    public function show(): ThemeResource
    {
        return ThemeResource::make(Theme::firstOrFail());
    }

    public function update(UpdateThemeRequest $request): ThemeResource
    {
        $data = $request->validated();

        $theme = Theme::firstOrFail();
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
