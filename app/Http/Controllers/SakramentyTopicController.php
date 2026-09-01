<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSakramentyTopicRequest;
use App\Http\Requests\UpdateSakramentyTopicRequest;
use App\Http\Requests\UploadContentImageRequest;
use App\Http\Resources\SakramentyTopicResource;
use App\Models\SakramentyTopic;
use App\Services\SakramentyTopicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SakramentyTopicController extends Controller
{
    public function __construct(private readonly SakramentyTopicService $sakramentyTopicService) {}

    public function index(): AnonymousResourceCollection
    {
        $topics = SakramentyTopic::with('author')->visible()->orderBy('order')->get();

        return SakramentyTopicResource::collection($topics);
    }

    public function manage(): AnonymousResourceCollection
    {
        $topics = SakramentyTopic::with('author')->orderBy('order')->get();

        return SakramentyTopicResource::collection($topics);
    }

    public function store(StoreSakramentyTopicRequest $request): JsonResponse
    {
        $topic = $this->sakramentyTopicService->create($request->validated());

        return SakramentyTopicResource::make($topic->load('author'))->response()->setStatusCode(201);
    }

    public function update(UpdateSakramentyTopicRequest $request, SakramentyTopic $sakramentyTopic): SakramentyTopicResource
    {
        $topic = $this->sakramentyTopicService->update($sakramentyTopic, $request->validated());

        return SakramentyTopicResource::make($topic->load('author'));
    }

    public function destroy(SakramentyTopic $sakramentyTopic): Response
    {
        $sakramentyTopic->delete();

        return response()->noContent();
    }

    public function uploadImage(UploadContentImageRequest $request): JsonResponse
    {
        $url = $this->sakramentyTopicService->storeImage(
            $request->file('image'),
            $request->getSchemeAndHttpHost()
        );

        return response()->json(['url' => $url]);
    }
}
