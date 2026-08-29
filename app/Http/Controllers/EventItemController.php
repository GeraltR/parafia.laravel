<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventItemRequest;
use App\Http\Requests\UpdateEventItemRequest;
use App\Http\Resources\EventItemResource;
use App\Models\EventItem;
use App\Services\EventItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class EventItemController extends Controller
{
    public function __construct(private readonly EventItemService $eventItemService) {}

    public function index(): AnonymousResourceCollection
    {
        return EventItemResource::collection(
            EventItem::with('author')
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->limit(4)
                ->get()
        );
    }

    public function manage(): AnonymousResourceCollection
    {
        return EventItemResource::collection(
            EventItem::with('author')->orderByDesc('date')->orderByDesc('time')->get()
        );
    }

    public function store(StoreEventItemRequest $request): JsonResponse
    {
        $eventItem = $this->eventItemService->create($request->validated());

        return EventItemResource::make($eventItem->load('author'))->response()->setStatusCode(201);
    }

    public function update(UpdateEventItemRequest $request, EventItem $eventItem): EventItemResource
    {
        $eventItem = $this->eventItemService->update($eventItem, $request->validated());

        return EventItemResource::make($eventItem->load('author'));
    }

    public function destroy(EventItem $eventItem): Response
    {
        $eventItem->delete();

        return response()->noContent();
    }
}
