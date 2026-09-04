<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInfoItemRequest;
use App\Http\Requests\UpdateInfoItemRequest;
use App\Http\Requests\UploadInfoItemImageRequest;
use App\Http\Resources\InfoItemResource;
use App\Models\InfoItem;
use App\Services\InfoItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class InfoItemController extends Controller
{
    public function __construct(private readonly InfoItemService $infoItemService) {}

    public function index(): AnonymousResourceCollection
    {
        $today = Carbon::today()->toDateString();

        return InfoItemResource::collection(
            InfoItem::with('author')
                ->whereDate('valid_from', '<=', $today)
                ->whereDate('valid_to', '>=', $today)
                ->orderByDesc('valid_from')
                ->get()
        );
    }

    public function manage(): AnonymousResourceCollection
    {
        return InfoItemResource::collection(
            InfoItem::with('author')->orderByDesc('valid_from')->get()
        );
    }

    public function store(StoreInfoItemRequest $request): JsonResponse
    {
        $infoItem = $this->infoItemService->create($request->validated());

        return InfoItemResource::make($infoItem->load('author'))->response()->setStatusCode(201);
    }

    public function update(UpdateInfoItemRequest $request, InfoItem $infoItem): InfoItemResource
    {
        $infoItem = $this->infoItemService->update($infoItem, $request->validated());

        return InfoItemResource::make($infoItem->load('author'));
    }

    public function destroy(InfoItem $infoItem): Response
    {
        $infoItem->delete();

        return response()->noContent();
    }

    public function uploadImage(UploadInfoItemImageRequest $request): JsonResponse
    {
        $url = $this->infoItemService->storeImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost(),
            $request->user()?->id
        );

        return response()->json(['url' => $url]);
    }
}
