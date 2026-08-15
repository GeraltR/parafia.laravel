<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsItemResource;
use App\Models\NewsItem;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsItemController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return NewsItemResource::collection(
            NewsItem::orderByDesc('date')->get()
        );
    }
}
