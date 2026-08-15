<?php

namespace App\Http\Controllers;

use App\Http\Resources\InfoExtraResource;
use App\Models\InfoExtra;

class InfoExtraController extends Controller
{
    public function show(): InfoExtraResource
    {
        return InfoExtraResource::make(InfoExtra::firstOrFail());
    }
}
