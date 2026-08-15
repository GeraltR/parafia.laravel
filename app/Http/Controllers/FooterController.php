<?php

namespace App\Http\Controllers;

use App\Http\Resources\FooterResource;
use App\Models\FooterConfig;

class FooterController extends Controller
{
    public function show(): FooterResource
    {
        return FooterResource::make(
            FooterConfig::with(['officeHours', 'legalLinks'])->firstOrFail()
        );
    }
}
