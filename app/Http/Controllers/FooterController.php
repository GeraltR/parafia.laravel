<?php

namespace App\Http\Controllers;

use App\Http\Resources\FooterResource;
use App\Models\FooterConfig;

class FooterController extends Controller
{
    public function show(): FooterResource
    {
        $footer = FooterConfig::firstOrCreate();
        $footer->wasRecentlyCreated = false;
        $footer->load(['officeHours', 'legalLinks']);

        return FooterResource::make($footer);
    }
}
