<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShortActionItemResource;
use App\Models\ShortActionItem;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShortActionItemController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ShortActionItemResource::collection(ShortActionItem::all());
    }
}
