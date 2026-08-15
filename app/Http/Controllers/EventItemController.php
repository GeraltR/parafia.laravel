<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventItemResource;
use App\Models\EventItem;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventItemController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return EventItemResource::collection(
            EventItem::orderBy('date')->orderBy('time')->get()
        );
    }
}
