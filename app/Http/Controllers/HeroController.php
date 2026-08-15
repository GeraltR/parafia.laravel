<?php

namespace App\Http\Controllers;

use App\Http\Resources\HeroResource;
use App\Models\Hero;

class HeroController extends Controller
{
    public function show(): HeroResource
    {
        return HeroResource::make(Hero::with('buttons')->firstOrFail());
    }
}
