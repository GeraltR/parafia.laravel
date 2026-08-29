<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsItemRequest;
use App\Http\Requests\UpdateNewsItemRequest;
use App\Http\Requests\UploadNewsImageRequest;
use App\Http\Resources\NewsItemResource;
use App\Models\NewsItem;
use App\Services\NewsItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NewsItemController extends Controller
{
    public function __construct(private readonly NewsItemService $newsItemService) {}

    public function index(): AnonymousResourceCollection
    {
        return NewsItemResource::collection(
            NewsItem::with('author')->orderByDesc('date')->limit(4)->get()
        );
    }

    public function manage(): AnonymousResourceCollection
    {
        return NewsItemResource::collection(
            NewsItem::with('author')->orderByDesc('date')->get()
        );
    }

    public function store(StoreNewsItemRequest $request): JsonResponse
    {
        $newsItem = $this->newsItemService->create($request->validated());

        return NewsItemResource::make($newsItem->load('author'))->response()->setStatusCode(201);
    }

    public function update(UpdateNewsItemRequest $request, NewsItem $newsItem): NewsItemResource
    {
        $newsItem = $this->newsItemService->update($newsItem, $request->validated());

        return NewsItemResource::make($newsItem->load('author'));
    }

    public function destroy(NewsItem $newsItem): Response
    {
        $newsItem->delete();

        return response()->noContent();
    }

    public function uploadImage(UploadNewsImageRequest $request): JsonResponse
    {
        $url = $this->newsItemService->storeImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost()
        );

        return response()->json(['url' => $url]);
    }
}
